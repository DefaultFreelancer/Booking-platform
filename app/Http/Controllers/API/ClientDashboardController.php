<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class ClientDashboardController extends Controller
{
    public function getData()
    {
        $user = Auth::user()->id;
        $totalBooking = 0;
        $pendingBooking = 0;
        $confirmBooking = 0;
        $cancelBooking = 0;

        $bookingStatus = Booking::where('user_id', $user)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($bookingStatus as $booking) {

            if ($booking->status == 'pending') {
                $pendingBooking = $booking->count;

            } else if ($booking->status == 'confirmed') {
                $confirmBooking = $booking->count;

            } else {
                $cancelBooking = $booking->count;
            }

            $totalBooking += $booking->count;
        }

        return [
            'totalBooking' => $totalBooking,
            'pendingBooking' => $pendingBooking,
            'confirmBooking' => $confirmBooking,
            'cancelBooking' => $cancelBooking,
        ];
    }

    public function getBookingData(Request $request)
    {
        // Initialize variables
        $id = 0;
        $from = 0;
        $to = 0;

        $user = Auth::user()->id;

        if ($request->filtersData) {
            $filtersData = $request->filtersData;

            foreach ($filtersData as $filter) {

                if ($filter['key'] === 'status') $id = $filter['value'];
                elseif ($filter['key'] === 'date_range') {
                    $from = $filter['value'][0][0] . '-' . $filter['value'][0][1] . '-' . $filter['value'][0][2];
                    $to = $filter['value'][1][0] . '-' . $filter['value'][1][1] . '-' . $filter['value'][1][2];
                }
            }
        }

        $settingAll = new AllSettingsFormat;

        $data = '';

        if ($request->columnKey != 'bill') {

            if ($id == 0 && $from == 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('user_id', $user)->count();
            }

            if ($id == 1 && $from == 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('status', 'confirmed')->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'confirmed')->where('user_id', $user)->count();
            }

            if ($id == 2 && $from == 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('status', 'pending')->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->where('user_id', $user)->count();
            }

            if ($id == 3 && $from == 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('status', 'canceled')->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->where('user_id', $user)->count();
            }

            if ($id == 0 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('booking_date', '>=', $from)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '>=', $from)->where('user_id', $user)->count();
            }

            if ($id == 1 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 2 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 3 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where([['status', '=', 'canceled'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'canceled'], ['booking_date', '>=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 0 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('booking_date', '<=', $from)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '<=', $from)->where('user_id', $user)->count();
            }

            if ($id == 1 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 2 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 3 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 0 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::whereBetween('booking_date', [$from, $to])->where('user_id', $user)->count();
            }

            if ($id == 1 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('status', '=', 'confirmed')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', '=', 'confirmed')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->count();
            }

            if ($id == 2 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('status', '=', 'pending')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->count();
            }

            if ($id == 3 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('user_id', $user)->where('status', '=', 'canceled')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->count();
            }

        } else {

            if ($id == 0 && $from == 0 && $to == 0) {
                $data = Booking::join('users', 'bookings.user_id', '=', 'users.id')->where('user_id', $user)->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('user_id', $user)->count();
            }

            if ($id == 1 && $from == 0 && $to == 0) {
                $data = Booking::where('status', 'confirmed')->where('user_id', $user)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'confirmed')->where('user_id', $user)->count();
            }

            if ($id == 2 && $from == 0 && $to == 0) {
                $data = Booking::where('status', 'pending')->where('user_id', $user)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->where('user_id', $user)->count();
            }

            if ($id == 3 && $from == 0 && $to == 0) {
                $data = Booking::where('status', 'canceled')->where('user_id', $user)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->where('user_id', $user)->count();
            }

            if ($id == 0 && $from != 0 && $to == 0) {
                $data = Booking::where('booking_date', '>=', $from)->where('user_id', $user)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '>=', $from)->where('user_id', $user)->count();
            }

            if ($id == 1 && $from != 0 && $to == 0) {
                $data = Booking::where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->where('user_id', $user)->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 2 && $from != 0 && $to == 0) {
                $data = Booking::where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->where('user_id', $user)->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 3 && $from != 0 && $to == 0) {
                $data = Booking::where([['status', '=', 'canceled'], ['booking_date', '>=', $from]])->where('user_id', $user)->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '<=', $from)->where('user_id', $user)->count();
            }

            if ($id == 0 && $from == 0 && $to != 0) {
                $data = Booking::where('booking_date', '<=', $from)->join('users', 'bookings.user_id', '=', 'users.id')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::whereBetween('booking_date', [$from, $to])->count();
            }

            if ($id == 1 && $from == 0 && $to != 0) {
                $data = Booking::where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->count();
            }

            if ($id == 2 && $from == 0 && $to != 0) {
                $data = Booking::where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->count();
            }

            if ($id == 3 && $from == 0 && $to != 0) {
                $data = Booking::where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->where('user_id', $user)->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->where('user_id', $user)->count();
            }

            if ($id == 0 && $from != 0 && $to != 0) {
                $data = Booking::whereBetween('booking_date', [$from, $to])->where('user_id', $user)->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::whereBetween('booking_date', [$from, $to])->where('user_id', $user)->count();
            }

            if ($id == 1 && $from != 0 && $to != 0) {
                $data = Booking::where('status', '=', 'confirmed')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', '=', 'confirmed')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->count();
            }

            if ($id == 2 && $from != 0 && $to != 0) {
                $data = Booking::where('status', '=', 'pending')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->count();
            }

            if ($id == 3 && $from != 0 && $to != 0) {
                $data = Booking::where('status', '=', 'canceled')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('users', 'bookings.user_id', '=', 'users.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'users.first_name', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->where('user_id', $user)->whereBetween('booking_date', [$from, $to])->count();
            }
        }

        foreach ($data as $singleRowData) {

            $singleRowData['payment_stat'] = $singleRowData->booking_bill - $singleRowData->bill;
            $singleRowData->booking_bill = $settingAll->getCurrency((string)($settingAll->thousandSep($singleRowData->booking_bill)));
            $singleRowData->booking_time = $settingAll->timeFormat(unserialize($singleRowData->booking_time));
            $singleRowData->booking_date = $settingAll->getDate($singleRowData->booking_date);
        }

        return ['dataRows' => $data, 'count' => $totalCount];
    }
}
