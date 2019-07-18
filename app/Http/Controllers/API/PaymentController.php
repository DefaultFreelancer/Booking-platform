<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Libraries\PayPalClient;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

class PaymentController extends Controller
{
    public function paymentIndex()
    {
        $data = PaymentMethod::all();

        return $data;
    }

    public function getAllMethods()
    {
        $data = PaymentMethod::where('available_to_client', 1)->where('enable', 1)->get();

        foreach ($data as $datum) {

            $keys = unserialize($datum->option);
            $datum->option = $keys[1];
        }

        return $data;
    }

    public function index()
    {
        $data = PaymentMethod::all();
        $totalCount = PaymentMethod::count();

        return ['dataRows' => $data, 'count' => $totalCount];
    }

    public function show($id)
    {
        $data = PaymentMethod::find($id);
        $data->option = unserialize($data->option);

        return $data;
    }

    public function store(Request $request)
    {
        PaymentMethod::create(['title' => $request->title, 'type' => 'custom', 'available_to_client' => $request->available_to_client, 'enable' => $request->enable]);
    }


    public function updatePayment(Request $request, $id)
    {
        try {
            $payment = PaymentMethod::find($id);

            if ($request->publickey) {
                $request['option'] = serialize([$request->secretkey, $request->publickey]);

            } elseif ($request->clientId) {
                $request['option'] = serialize([$request->secretkey, $request->clientId,$request->paypalMode]);

            }

            $payment->update($request->all());

        } catch (\Exception $e) {

            return $e;
        }

    }

    public function paymentFunction(Request $request)
    {
        $keys = PaymentMethod::select('option')->where('type', 'stripe')->first();
        $keys = unserialize($keys->option);
        $currency_code = Setting::select('setting_value')->where('setting_name', 'currency_code')->first()->setting_value;

        \Stripe\Stripe::setApiKey($keys[0]);
        // Token is created using Checkout or Elements!
        // Get the payment token ID submitted by the form:
        $token = $request->token;
        // Charge the user's card:

        if ($charge = \Stripe\Charge::create(array(
            "amount" => $request->bill * 100,
            "currency" => $currency_code,
            "description" => Lang::get('lang.example_charge'),
            "source" => $token,
        ))) {

            $rand_id = str_random(20);
            $responce = ['payment' => 'done'];

            return response()->json($responce, 200);

        } else {
            $responce = ['isvalid' => false];

            return response()->json($responce, 401);
        }


    }

    // Due pay by Admin
    public function manualPayment(Request $request)
    {
        Payment::where('booking_id', $request->booking_id)
            ->update(['bill' => Payment::select('bill')
                    ->where('booking_id', $request->booking_id)
                    ->first()->bill + $request->bill, 'method_id' => $request->method_id, 'created_by' => Auth::user()->id]);

    }

    public function duePayment($id)
    {
        $allSettings = new AllSettingsFormat;
        $paymentDetails = Payment::join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->select('payments.*', 'bookings.status', 'bookings.booking_bill')
            ->where('booking_id', $id)->first();

        $paymentDetails['payment_stat'] = $allSettings->getCurrency($paymentDetails->booking_bill - $paymentDetails->bill);
        $paymentDetails->booking_bill = $allSettings->getCurrency($paymentDetails->booking_bill);
        $paymentMethods = PaymentMethod::select('id', 'title')->where('type', 'custom')->where('enable', 1)->get();
        $data = ['paymentDetails' => $paymentDetails, 'paymentMethods' => $paymentMethods];

        return $data;
    }

    public function destroy($id)
    {
        $paymentmethods = PaymentMethod::find($id);
        $paymentmethods->delete();
    }

    public function paypalPayment(Request $request)
    {
        $orderId = $request->orderID;

        try{
            $client =  PayPalClient::client();
            $response = $client->execute(new OrdersGetRequest($orderId));
            $details= $response->result;
            if($orderId == $details->id){

                return "success";
            }

        }catch(\Exception $error){

            return $error;
        }


    }
}
