<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\Libraries\Email;
use App\Libraries\Permissions;
use App\Models\Booking;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Services;
use App\Models\Setting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use PDF;

class BookingController extends Controller
{
    public function bookingIndex()
    {
        return view('booking.index');
    }

    public function index($id)
    {
        return view('booking.bookingInfoForm', ['id' => $id]);
    }

    public function invoiceData($column,$id){

        $invoiceData  = Invoice::Join('users', 'invoices.user_id', '=', 'users.id')
            ->leftjoin('payments', 'invoices.booking_id', '=', 'payments.booking_id')
            ->select('invoices.*','users.first_name', 'users.last_name', 'users.email', 'users.phone', 'payments.bill'  )
            ->where($column,$id)
            ->first();

        $invoiceItemData = InvoiceItem::where('invoice_id',$invoiceData->id)->get();

        $invoiceLogo = Setting::where('setting_name','invoice_logo')->first();
        $companyName = Setting::where('setting_name','company_name')->first();
        $companyInfo = Setting::where('setting_name','company_info')->first();

        $allSet = new AllSettingsFormat;

        foreach ($invoiceItemData as $item) {
            $booking  = $item->booking_time;
            $booking = unserialize($booking);
            $item->booking_time = $booking[0];
            $item->unit_price = $allSet->getCurrency($item->unit_price) ;
            $item->total = $allSet->getCurrency($item->total);

            return array(
                'name' => $invoiceData->first_name." ".$invoiceData->last_name,
                'email'=>$invoiceData->email,
                'phone' => $invoiceData->phone,
                'invoiceId'=>$invoiceData->id,
                'due' => $allSet->getCurrency($invoiceData->total),
                'total' => $allSet->getCurrency($invoiceData->due),
                'invoiceCreatedAt' =>$allSet->getDate($invoiceData->created_at),
                'paid' =>  $allSet->getCurrency($invoiceData->total-$invoiceData->due),
                'invoiceItemData' =>$invoiceItemData,
                'invoiceLogo' =>$invoiceLogo->setting_value,
                'companyName' =>$companyName->setting_value,
                'companyInfo' =>$companyInfo->setting_value,
            );
        }
    }
    public function sendPdf($invoiceId,$mailText, $email, $subject){

        $data = $this->invoiceData('invoices.id',$invoiceId);
        $fileNameToStore = 'Booking-'.$invoiceId.'.pdf';
        $pdf = PDF::loadView('invoice.invoice', $data);
        $pdf->save('uploads/pdf/'.$fileNameToStore);

        $emailSend = new Email;
         $emailSend->sendEmail($mailText, $email, $subject,$fileNameToStore);
            unlink(public_path('uploads/pdf/'.$fileNameToStore));
        }

    public function generateInvoice($id){

        $data = $this->invoiceData('invoices.booking_id',$id);
        $pdf = PDF::loadView('invoice.invoice', $data);

        return $pdf->stream('invoice.pdf');
    }

    public function setBooking(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'seat' => 'required',
        ]);

        $allSet = new AllSettingsFormat;
        $isAdmin = new Permissions;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $phone = $request->phone_number;
        $token = str_random(25);
        $booking_time = serialize($allSet->setTimeFormat($request->slot));
        $quantity = $request->seat;
        $payment_id = $request->payment_id;
        $random_id = $request->random_id;
        $currency_code = $allSet->currencyCode();

        $user = User::where('email', '=', $email)->first();

        if ($user == null) {
            User::create(['first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'phone' => $phone, 'token' => $token]);
        }

        $serviceDetails = Services::select('price', 'title', 'service_duration_type', 'auto_confirm')->where('id', $id)->first();

        $user_id = User::select('id')->where('email', $email)->first();

        $price = $serviceDetails->price;
        $booking_date = $request->booking_date;
        $comment = $request->comment;
        $title = $serviceDetails->title;
        $serviceType = $serviceDetails->service_duration_type;
        $auto_conf = $serviceDetails->auto_confirm;
        $method_id = $request->method_id;
        $bill = 0;
        $payment = '';

        $appName = Setting::select('setting_value')->where('setting_name', 'email_from_name')->first()->setting_value;

        if ($request->duration_type == 'daily') {
            $bill = $quantity * $price;

        } else {
            $bill = $quantity * $price * sizeof($request->slot);
        }

        if ($isAdmin->isAdmin() || $auto_conf == 1) {
            $booking_id = Booking::create(['service_id' => $id, 'user_id' => $user_id->id, 'phone_number' => $phone, 'status' => 'confirmed', 'booking_date' => $booking_date, 'booking_time' => $booking_time, 'quantity' => $quantity, 'comment' => $comment, 'booking_bill' => $bill]);

            // invoice related code start here
            $invoiceStore = Invoice::create(['user_id' => $user_id->id, 'booking_id'=>$booking_id->id , 'total' => $bill, 'due' => $bill, 'created_by' => '', 'comment' => $comment]);
            $invoiceId = $invoiceStore->id;
            InvoiceItem::create(['invoice_id' => $invoiceId, 'service_title' => $title, 'booking_date' => $booking_date, 'booking_time' => $booking_time, 'unit_price' => $price, 'quantity' => $quantity,'total' => $bill]);

            $b_id = $booking_id->id;

            if ($request->bill) Payment::create(['booking_id' => $b_id, 'currency_code' => $currency_code, 'bill' => $request->bill, 'method_id' => $request->method_id]);
            else Payment::create(['booking_id' => $b_id, 'currency_code' => $currency_code, 'bill' => 0, 'method_id' => 0]);

            $content = EmailTemplate::select('template_subject', 'default_content', 'custom_content')->where('template_type', 'booking_confirmation')->first();

            $subject = $content->template_subject;

            if ($content->custom_content) {
                $text = $content->custom_content;

            } else {
                $text = $content->default_content;
            }

            if ($request->bill < $bill) {
                $payment = Lang::get('lang.due');

            } else {
                $payment = Lang::get('lang.paid');
            }

            $b_time = array();
            $booking_time = unserialize($booking_time);

            foreach ($booking_time as $item) {

                $item = $allSet->timeFormat($item);
                array_push($b_time, $item);
            }

            $b_time = serialize($b_time);

            if ($serviceType === 'daily') {
                $mailText = str_replace('{booking_id}', $b_id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_quantity}', $quantity, str_replace('{booking_status}', 'Confirmed', str_replace('{bill}', $allSet->getCurrency($bill), str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', '-', str_replace('{payment_status}', $payment, $text)))))))))));

            } else {
                $mailText = str_replace('{booking_id}', $b_id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_quantity}', $quantity, str_replace('{booking_status}', 'Confirmed', str_replace('{bill}', $allSet->getCurrency($bill), str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', implode(', ', unserialize($b_time)), str_replace('{payment_status}', $payment, $text)))))))))));
            }
            //pdf related code start
           $this->sendPdf($invoiceId,$mailText, $email, $subject);

        } else {
          
            $transaction_id = $request->transaction_id;

            $booking_id = Booking::create(['service_id' => $id, 'user_id' => $user_id->id, 'phone_number' => $phone, 'booking_date' => $booking_date, 'booking_time' => $booking_time, 'quantity' => $quantity, 'comment' => $comment, 'booking_bill' => $bill]);

            // invoice related code start here
            $invoiceStore = Invoice::create(['user_id' => $user_id->id, 'booking_id'=>$booking_id->id , 'total' => $bill, 'due' => $bill, 'created_by' => '', 'comment' => $comment]);
            $invoiceId = $invoiceStore->id;
            InvoiceItem::create(['invoice_id' => $invoiceId, 'service_title' => $title, 'booking_date' => $booking_date, 'booking_time' => $booking_time, 'unit_price' => $price, 'quantity' => $quantity,'total' => $bill]);

            $b_id = $booking_id->id;

            if ($request->bill) Payment::create(['booking_id' => $b_id, 'currency_code' => $currency_code, 'bill' => $request->bill, 'method_id' => $request->method_id,'transaction_id' => $transaction_id]);
            else Payment::create(['booking_id' => $b_id, 'currency_code' => $currency_code, 'bill' => 0, 'method_id' => 0,'transaction_id' => $transaction_id]);

            $content = EmailTemplate::select('template_subject', 'default_content', 'custom_content')->where('template_type', 'booking_received')->first();

            $subject = $content->template_subject;

            if ($content->custom_content) {
                $text = $content->custom_content;

            } else {
                $text = $content->default_content;
            }

            if ($request->bill < $bill) {
                $payment = Lang::get('lang.due');

            } else {
                $payment = Lang::get('lang.paid');
            }

            $b_time = array();
            $booking_time = unserialize($booking_time);

            foreach ($booking_time as $item) {

                $item = $allSet->timeFormat($item);
                array_push($b_time, $item);
            }

            $b_time = serialize($b_time);

            if ($serviceType === 'daily') {
                $mailText = str_replace('{booking_id}', $b_id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_quantity}', $quantity, str_replace('{booking_status}', 'Pending', str_replace('{bill}', $allSet->getCurrency($bill), str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', '-', str_replace('{payment_status}', $payment, $text)))))))))));

            } else {
                $mailText = str_replace('{booking_id}', $b_id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_quantity}', $quantity, str_replace('{booking_status}', 'Pending', str_replace('{bill}', $allSet->getCurrency($bill), str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', implode(', ', unserialize($b_time)), str_replace('{payment_status}', $payment, $text)))))))))));
            }
            $this->sendPdf($invoiceId,$mailText, $email, $subject);
        }

        $notifcations = new Notification();
        $notifcations->event = 'submitted_a_new_booking';
        $notifcations->booking_id = $b_id;
        $notifcations->booking_by = User::select('id')->where('email', $email)->first()->id;
        $notifcations->activity_id = $notifcations->booking_by;

        if (Auth::user()) {
            $notifcations->notify_to = User::select('id')->where('is_admin', '1')->where('id', '!=', Auth::user()->id)->get(); //send notifications to users
        } else {
            $notifcations->notify_to = User::select('id')->where('is_admin', '1')->get(); //send notifications to users
        }

        $notify = '';

        foreach ($notifcations->notify_to as $notify_to) {

            $notify = $notify . ',' . $notify_to->id;
        }

        $notifcations->notify_to = $notify;
        $notifcations->save();
    }

    public function action(Request $request, $id)
    {
        $allSet = new AllSettingsFormat;
        $status = Booking::find($id);
        $quantity = $status->quantity;
        $booking_date = $status->booking_date;
        $booking_time = array();
        $b_time = unserialize($status->booking_time);

        foreach ($b_time as $item) {

            $item = $allSet->timeFormat($item);
            array_push($booking_time, $item);
        }

        $serviceDetails = Services::select('price','title','service_duration_type')->where('id', $status->service_id)->first();

        $price = $serviceDetails->price;
        $title = $serviceDetails->title;
        $serviceType = $serviceDetails->service_duration_type;

        $user = User::select('id', 'first_name', 'last_name', 'email')->where('id', $status->user_id)->first();

        $email = $user->email;
        $first_name = $user->first_name;
        $last_name = $user->last_name;

        $appName = Setting::select('setting_value')->where('setting_name', 'email_from_name')->first()->setting_value;
        $auth_user = Auth::user();

        if ($request->status == 'confirmed') {
            $notifcations = new Notification();
            $notifcations->event = 'confirmed_the_booking';
            $notifcations->booking_id = $id;

            $notifcations->booking_by = User::select('id')->where('email', $email)->first()->id;
            $notifcations->activity_id = $auth_user->id;

            $notifcations->notify_to = User::select('id')->where('is_admin', '1')->where('id', '!=', Auth::id())->get(); //send notifications to users
            $payment_stat = Payment::select('bill')->where('booking_id', $status->id)->first()->bill;

            $notify = '';

            foreach ($notifcations->notify_to as $notify_to) {

                $notify = $notify . ',' . $notify_to->id;
            }

            $notifcations->notify_to = $notify;
            $notifcations->save();

            if ($payment_stat < $status->booking_bill) {
                $payment = Lang::get('lang.due');

            } else {
                $payment = Lang::get('lang.paid');
            }

            $content = EmailTemplate::select('template_subject', 'default_content', 'custom_content')->where('template_type', 'booking_confirmation')->first();
            $subject = $content->template_subject;

            if ($content->custom_content) {
                $text = $content->custom_content;

            } else {
                $text = $content->default_content;
            }

            $bill = 0;

            if (sizeof($booking_time) == 0) {
                $bill = $quantity * $price;

            } else {
                $bill = $quantity * $price * sizeof($booking_time);
            }

            if ($serviceType === 'daily') {
                $mailText = str_replace('{booking_id}', $status->id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_quantity}', $quantity, str_replace('{booking_status}', 'Confirmed', str_replace('{bill}', $allSet->getCurrency($bill), str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', '-', str_replace('{payment_status}', $payment, $text)))))))))));

            } else {
                $mailText = str_replace('{booking_id}', $status->id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_quantity}', $quantity, str_replace('{booking_status}', 'Confirmed', str_replace('{bill}', $allSet->getCurrency($bill), str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', implode(', ', $booking_time), str_replace('{payment_status}', $payment, $text)))))))))));
            }

            $emailSend = new Email;
            $emailSend->sendEmail($mailText, $email, $subject);

        } else {
            $notifcations = new Notification();
            $notifcations->event = 'canceled_the_booking';
            $notifcations->booking_id = $id;

            $notifcations->booking_by = User::select('id')->where('email', $email)->first()->id;
            $notifcations->activity_id = $auth_user->id;

            $notifcations->notify_to = User::select('id')->where('is_admin', '1')->where('id', '!=', Auth::id())->get(); //send notifications to users

            $notify = '';

            foreach ($notifcations->notify_to as $notify_to) {

                $notify = $notify . ',' . $notify_to->id;
            }

            $notifcations->notify_to = $notify;
            $notifcations->save();

            $content = EmailTemplate::select('template_subject', 'default_content', 'custom_content')->where('template_type', 'booking_rejected')->first();
            $subject = $content->template_subject;

            if ($content->custom_content) {
                $text = $content->custom_content;

            } else {
                $text = $content->default_content;
            }

            if ($serviceType === 'daily') {
                $mailText = str_replace('{booking_id}', $status->id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_status}', 'Canceled', str_replace('{app_name}', $appName, str_replace('{booking_quantity}', $quantity, str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', '-', $text))))))))));

            } else {
                $mailText = str_replace('{booking_id}', $status->id, str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{service_title}', $title, str_replace('{booking_status}', 'Canceled', str_replace('{app_name}', $appName, str_replace('{booking_quantity}', $quantity, str_replace('{app_name}', $appName, str_replace('{booking_date}', $allSet->getDate($booking_date), str_replace('{booking_slot}', implode(', ', $booking_time), $text))))))))));
            }

            $emailSend = new Email;
            $emailSend->sendEmail($mailText, $email, $subject);
        }

        $status->update($request->all());
    }


    public function getBookingData(Request $request)
    {
        $id = 0;
        $from = 0;
        $to = 0;
        $paymentStatus = 0;
        $status = '';

        if ($request->filtersData) {
            foreach ($request->filtersData as $filter) {

                if ($filter['key'] === 'status') $id = $filter['value'];
                elseif ($filter['key'] === 'payment_status') $paymentStatus = $filter['value'];
                elseif ($filter['key'] === 'date_range') {
                    $from = $filter['value'][0][0] . '-' . $filter['value'][0][1] . '-' . $filter['value'][0][2];
                    $to = $filter['value'][1][0] . '-' . $filter['value'][1][1] . '-' . $filter['value'][1][2];
                }
            }
        }

        $settingAll = new AllSettingsFormat;
        $data = '';
        $searchValue = $request->searchValue;

        $joinedTable = Booking::join('users', 'bookings.user_id', '=', 'users.id')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->join('payments', 'bookings.id', '=', 'payments.booking_id')
            ->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill');

        //Search query: search by Client Id, Service Title and Client Name
        $searchQuery = [
            ['bookings.id', 'LIKE', '%' . $searchValue . '%', 'or'],
            ['services.title', 'LIKE', '%' . $searchValue . '%', 'or'],
            ['users.first_name', 'LIKE', '%' . $searchValue . '%', 'or']

        ];

        //status filter values
        if ($id == 1) $status = 'confirmed';
        if ($id == 2) $status = 'pending';
        if ($id == 3) $status = 'canceled';

        //Status filter: All, Payment Status filter:All, date range:None, search value:False
        if ($id == 0 && $paymentStatus == 0 && $from == 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable;
        } //Status filter: All, Payment Status filter:Due , date range:None, search value:False
        elseif ($id == 0 && $paymentStatus == 1 && $from == 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) {
                $query->where(DB::raw('bookings.booking_bill-payments.bill '), '!=', 0);
            });
        } //Status filter: All, Payment Status filter:Paid, date range:None, search value:False
        elseif ($id == 0 && $paymentStatus == 2 && $from == 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) {
                $query->where(DB::raw('bookings.booking_bill-payments.bill '), '=', 0);
            });
        } //Status filter: All, Payment Status filter:All,  date range:From and To, search value:True
        elseif ($id == 0 && $paymentStatus == 0 && $from != 0 && $to != 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All, Payment Status filter:Due ,  date range:From and To, search value:True
        elseif ($id == 0 && $paymentStatus == 1 && $from != 0 && $to != 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) {
                    $query->where([[DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All, Payment Status filter:Paid,  date range:From and To, search value:True
        elseif ($id == 0 && $paymentStatus == 2 && $from != 0 && $to != 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) {
                    $query->where(DB::raw('bookings.booking_bill-payments.bill '), '=', 0);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All, Payment Status filter:All,  date range:From and To, search value:false
        elseif ($id == 0 && $paymentStatus == 0 && $from != 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($from, $to) {
                $query->whereBetween('booking_date', [$from, $to]);
            });
        } //Status filter: All, Payment Status filter:Due ,  date range:From and To, search value:false
        elseif ($id == 0 && $paymentStatus == 1 && $from != 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) {
                    $query->where([[DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                });
        } //Status filter: All, Payment Status filter:Paid,  date range:From and To, search value:false
        elseif ($id == 0 && $paymentStatus == 2 && $from != 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) {
                    $query->where([[DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
                });
        } //Status filter: All, Payment Status filter:All, date range:From, search value:True
        elseif ($id == 0 && $paymentStatus == 0 && $from != 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from) {
                    $query->where('booking_date', '>=', $from);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All, Payment Status filter:Due, date range:From, search value:True
        elseif ($id == 0 && $paymentStatus == 1 && $from != 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from) {
                    $query->where([['booking_date', '>=', $from], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All, Payment Status filter:Paid, date range:From, search value:True
        elseif ($id == 0 && $paymentStatus == 2 && $from != 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from) {
                    $query->where([['booking_date', '>=', $from], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All,Payment Status filter:All, date range: from, search value:True
        elseif ($id == 0 && $paymentStatus == 0 && $from == 0 && $to == 0 && $searchValue) {
            $data = $joinedTable->where(function ($query) use ($searchValue, $searchQuery) {
                $query->where($searchQuery);
            });
        } //Status filter: All,Payment Status filter:Due, date range: from, search value:True
        elseif ($id == 0 && $paymentStatus == 1 && $from == 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) {
                    $query->where([[DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All,Payment Status filter:Paid, date range: from, search value:True
        elseif ($id == 0 && $paymentStatus == 2 && $from == 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) {
                    $query->where([[DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All, Payment Status filter:All  , date range:From, search value:false
        elseif ($id == 0 && $paymentStatus == 0 && $from != 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $from) {
                $query->where('booking_date', '>=', $from);
            });
        } //Status filter: All, Payment Status filter:Due  , date range:From, search value:false
        elseif ($id == 0 && $paymentStatus == 1 && $from != 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $from) {
                $query->where([[DB::raw('bookings.booking_bill-payments.bill '), '!=', 0], ['booking_date', '>=', $from]]);
            });
        } //Status filter: All, Payment Status filter:Paid  , date range:From, search value:false
        elseif ($id == 0 && $paymentStatus == 2 && $from != 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $from) {
                $query->where([[DB::raw('bookings.booking_bill-payments.bill '), '=', 0], ['booking_date', '>=', $from]]);
            });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Due  , date range:none, search value:true
        elseif ($id != 0 && $paymentStatus == 1 && $from == 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status) {
                    $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Paid  , date range:none, search value:true
        elseif ($id != 0 && $paymentStatus == 2 && $from == 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status) {
                    $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Due  , date range:none, search value:false
        elseif ($id != 0 && $paymentStatus == 1 && $from == 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status) {
                    $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Due  , date range:from and to, search value:false
        elseif ($id != 0 && $paymentStatus == 1 && $from != 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Paid  , date range:none, search value:false
        elseif ($id != 0 && $paymentStatus == 2 && $from == 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status) {
                $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
            });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Paid  , date range:from, search value:true
        elseif ($id != 0 && $paymentStatus == 2 && $from != 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], ['booking_date', '>=', $from], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Paid  ,date range:from, search value:false
        elseif ($id != 0 && $paymentStatus == 2 && $from != 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $from) {
                $query->where([['status', '=', $status], ['booking_date', '>=', $from], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
            });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:Paid  ,date range:from and to, search value:true
        elseif ($id != 0 && $paymentStatus == 2 && $from != 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:All,  no date range, no search value
        elseif ($id != 0 && $paymentStatus == 0 && $from == 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status) {
                $query->where('status', '=', $status);
            });
        } //Status filter (confirmed/pending/canceled),Payment Status filter:All, no date range, search value:true
        elseif ($id != 0 && $paymentStatus == 0 && $from == 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status, $from, $to) {
                    $query->where('status', '=', $status);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter (confirmed/pending/canceled),Payment Status filter:All, date range: from, search value:false
        elseif ($id != 0 && $paymentStatus == 0 && $from != 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], ['booking_date', '>=', $from]]);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:All, date range: from and to , search value:false
        elseif ($id != 0 && $paymentStatus == 0 && $from != 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) use ($status) {
                    $query->where('status', '=', $status);
                });
        } //Status filter (confirmed/pending/canceled), Payment Status filter:All, date range: from and to , search value:true
        elseif ($id != 0 && $paymentStatus == 0 && $from != 0 && $to != 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) use ($status) {
                    $query->where('status', '=', $status);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter (confirmed/pending/canceled),Payment Status filter:All, date range: from, search value:True
        elseif ($id != 0 && $paymentStatus == 0 && $from != 0 && $to == 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], ['booking_date', '>=', $from]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter (confirmed/pending/canceled),Payment Status filter:Due, date range: from , search value:True
        elseif ($id != 0 && $paymentStatus == 1 && $from != 0 && $to == 0 && $searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $from) {
                $query->where([['status', '=', $status], ['booking_date', '>=', $from], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
            })->where(function ($query) use ($searchValue, $searchQuery) {
                $query->where($searchQuery);
            });
        } //Status filter (confirmed/pending/canceled),Payment Status filter:Due, date range: from , search value:False
        elseif ($id != 0 && $paymentStatus == 1 && $from != 0 && $to == 0 && !$searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], ['booking_date', '>=', $from], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                });
        } //Status filter (confirmed/pending/canceled),Payment Status filter:Due, date range: from and to, search value:True
        elseif ($id != 0 && $paymentStatus == 1 && $from != 0 && $to != 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter (confirmed/pending/canceled),Payment Status filter:Paid, date range: from and to, search value:True
        elseif ($id != 0 && $paymentStatus == 2 && $from != 0 && $to != 0 && $searchValue) {
            $data = $joinedTable
                ->where(function ($query) use ($from, $to) {
                    $query->whereBetween('booking_date', [$from, $to]);
                })
                ->where(function ($query) use ($status, $from) {
                    $query->where([['status', '=', $status], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
                })
                ->where(function ($query) use ($searchValue, $searchQuery) {
                    $query->where($searchQuery);
                });
        } //Status filter: All, Payment Status filter:All  , date range:to, search value:false
        elseif ($id == 0 && $paymentStatus == 0 && $from == 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $to) {
                $query->where('booking_date', '<=', $to);
            });
        } //Status filter: All, Payment Status filter:Due  , date range:to, search value:false
        elseif ($id == 0 && $paymentStatus == 1 && $from == 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $to) {
                $query->where([['booking_date', '<=', $to], [DB::raw('bookings.booking_bill-payments.bill '), '!=', 0]]);
            });
        } //Status filter: All, Payment Status filter:Paid  , date range:none, search value:false
        elseif ($id == 0 && $paymentStatus == 2 && $from == 0 && $to != 0 && !$searchValue) {
            $data = $joinedTable->where(function ($query) use ($status, $to) {
                $query->where([['booking_date', '<=', $to], [DB::raw('bookings.booking_bill-payments.bill '), '=', 0]]);
            });
        }

        $totalCount = $data->count();
        $data = $data->orderBy('bookings.' . $request->columnKey, $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();

        foreach ($data as $singleRowData) {

            $singleRowData['payment_stat'] = $singleRowData->booking_bill - $singleRowData->bill;
            $singleRowData->booking_bill = $settingAll->getCurrency((string)($settingAll->thousandSep($singleRowData->booking_bill)));
            $singleRowData->booking_time = $settingAll->timeFormat(unserialize($singleRowData->booking_time));
            $singleRowData->booking_date = $settingAll->getDate($singleRowData->booking_date);
        }

        return ['dataRows' => $data, 'count' => $totalCount];
    }

    public function bookingDetails($id, $return_data = false)
    {
        $settingAll = new AllSettingsFormat;

        $booking = Booking::Join('services', 'bookings.service_id', '=', 'services.id')
            ->join('payments', 'bookings.id', '=', 'payments.booking_id')
            ->leftJoin('payment_methods', 'payments.method_id', '=', 'payment_methods.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->select('bookings.*', 'users.id as user_id', 'users.avatar', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'services.title', 'services.price', 'services.service_duration_type', 'payments.bill', 'payment_methods.title as method_title', 'payment_methods.id as payment_method_id', 'payments.id as payment_id')
            ->find($id);

        $booking['payment_stat'] = $booking->booking_bill - $booking->bill;
        $booking->bill = $settingAll->getCurrency((string)($settingAll->thousandSep(count(unserialize($booking->booking_time)) * $booking->quantity * $booking->price)));
        $booking->booking_time = $settingAll->timeFormat(unserialize($booking->booking_time));
        $booking->booking_date = $settingAll->getDate($booking->booking_date);

        if ($return_data == false) {
            return view('booking.view', ['booking' => $booking]);

        } else {
            return $booking;
        }
    }

    public function bookingPaymentDetails($id)
    {
        $settingAll = new AllSettingsFormat;

        $paymentDetails = Payment::leftJoin('payment_methods', 'payments.method_id', '=', 'payment_methods.id')
            ->leftJoin('users', 'payments.created_by', '=', 'users.id')
            ->select('payments.*', 'payment_methods.title as payment_method', 'users.first_name', 'users.last_name')
            ->find($id);

        $paymentDetails->created = $paymentDetails->created_at->format('m/d/Y');
        $paymentDetails->created = $settingAll->getDate($paymentDetails->created);

        $paymentDetails->bill = $settingAll->getCurrency($paymentDetails->bill);

        return $paymentDetails;
    }

}