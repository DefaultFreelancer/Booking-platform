<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\Libraries\Permissions;
use App\Models\Booking;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class ClientController extends Controller
{
    public function permissionCheck()
    {
        $controller = new Permissions;
        return $controller;
    }

    public function index()
    {
        return view('clients.index');
    }

    public function allClients(Request $request)
    {
        $searchValue = '';

        if ($request->columnKey) $columnName = $request->columnKey;
        if ($request->rowLimit) $limit = $request->rowLimit;
        $offset = $request->rowOffset;
        if ($request->searchValue) $searchValue = $request->searchValue;

        if ($columnName == 'full_name') $columnName = 'first_name';
        else if ($columnName == 'role_title') $columnName = 'role_id';

        if ($searchValue) {
            $query = User::query();
            $columns = ['first_name', 'last_name', 'email', 'phone'];

            foreach ($columns as $column) {

                $query->orWhere($column, 'LIKE', '%' . $searchValue . '%')->where('role_id', '=', 0)->where('is_admin', '=', 0)->get();
            }

            $users = $query->where('is_admin', '=', 0)
                ->where('role_id', '=', 0)
                ->orderBy($columnName, $request->columnSortedBy)
                ->take($limit)
                ->skip($offset)
                ->select('id', 'first_name', 'last_name', DB::raw("CONCAT(users.first_name,' ',users.last_name) as full_name"), 'email', 'phone')
                ->get();

            $totalCount = $query->where('is_admin', 0)->where('role_id', 0)->count();

        } else {
            $users = User::where('is_admin', 0)
                ->where('role_id', 0)
                ->orderBy($columnName, $request->columnSortedBy)
                ->take($limit)
                ->skip($offset)
                ->select('id', 'first_name', 'last_name', 'email', 'phone', DB::raw("CONCAT(users.first_name,' ',users.last_name) as full_name"))
                ->get();

            $totalCount = User::where('is_admin', 0)->where('role_id', 0)->count();
        }

        return ['dataRows' => $users, 'count' => $totalCount];
    }

    public function userDetails($id)
    {
        $user = User::find($id);
        return $user;
    }

    public function updateClient(Request $request, $id)
    {
        if ($this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
        ])) {
            $user = User::find($id);
            $user->update($request->all());

        } else {
            $response = [
                'msg' => Lang::get('lang.invalid_input'),
            ];

            return response()->json($response, 400);

        }
    }

    public function show($id)
    {
        $settingAll = new AllSettingsFormat;

        $user = User::find($id);
        $user->date_of_birth = $settingAll->getDate($user->date_of_birth);

        $totalBooking = Booking::where('user_id', $id)->count();

        return view('clients.view', ['user' => $user, 'totalbooking' => $totalBooking]);
    }

    public function clientBookingList(Request $request, $userid)
    {
        $id = 0;
        $from = 0;
        $to = 0;

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
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('user_id', $userid)->count();
            }

            if ($id == 1 && $from == 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('status', 'confirmed')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'confirmed')->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from == 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('status', 'pending')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from == 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('status', 'canceled')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->where('user_id', $userid)->count();
            }

            if ($id == 0 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('booking_date', '>=', $from)->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '>=', $from)->where('user_id', $userid)->count();
            }

            if ($id == 1 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from != 0 && $to == 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where([['status', '=', 'canceled'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'canceled'], ['booking_date', '>=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 0 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('booking_date', '<=', $from)->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '<=', $from)->where('user_id', $userid)->count();
            }

            if ($id == 1 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from == 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 0 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

            if ($id == 1 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('status', '=', 'confirmed')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', '=', 'confirmed')->whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('status', '=', 'pending')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from != 0 && $to != 0) {
                $data = Booking::orderBy($request->columnKey, $request->columnSortedBy)->where('status', '=', 'canceled')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

        } else {
            if ($id == 0 && $from == 0 && $to == 0) {
                $data = Booking::join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('user_id', $userid)->count();
            }

            if ($id == 1 && $from == 0 && $to == 0) {
                $data = Booking::where('status', 'confirmed')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'confirmed')->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from == 0 && $to == 0) {
                $data = Booking::where('status', 'pending')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from == 0 && $to == 0) {
                $data = Booking::where('status', 'canceled')->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->where('user_id', $userid)->count();
            }

            if ($id == 0 && $from != 0 && $to == 0) {
                $data = Booking::where('booking_date', '>=', $from)->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '>=', $from)->where('user_id', $userid)->count();
            }

            if ($id == 1 && $from != 0 && $to == 0) {
                $data = Booking::where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '>=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from != 0 && $to == 0) {
                $data = Booking::where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '>=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from != 0 && $to == 0) {
                $data = Booking::where([['status', '=', 'canceled'], ['booking_date', '>=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->where('user_id', $userid)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('booking_date', '<=', $from)->where('user_id', $userid)->count();
            }

            if ($id == 0 && $from == 0 && $to != 0) {
                $data = Booking::where('booking_date', '<=', $from)->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

            if ($id == 1 && $from == 0 && $to != 0) {
                $data = Booking::where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->take($request->rowLimit)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'confirmed'], ['booking_date', '<=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from == 0 && $to != 0) {
                $data = Booking::where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'pending'], ['booking_date', '<=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from == 0 && $to != 0) {
                $data = Booking::where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where([['status', '=', 'canceled'], ['booking_date', '<=', $from]])->where('user_id', $userid)->count();
            }

            if ($id == 0 && $from != 0 && $to != 0) {
                $data = Booking::whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

            if ($id == 1 && $from != 0 && $to != 0) {
                $data = Booking::where('status', '=', 'confirmed')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', '=', 'confirmed')->whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

            if ($id == 2 && $from != 0 && $to != 0) {
                $data = Booking::where('status', '=', 'pending')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'pending')->whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
            }

            if ($id == 3 && $from != 0 && $to != 0) {
                $data = Booking::where('status', '=', 'canceled')->whereBetween('booking_date', [$from, $to])->join('services', 'bookings.service_id', '=', 'services.id')->join('payments', 'bookings.id', '=', 'payments.booking_id')->select('bookings.*', 'services.title', 'services.price', 'payments.bill')->where('user_id', $userid)->orderBy(DB::raw('(services.price * bookings.quantity )'), $request->columnSortedBy)->take($request->rowLimit)->skip($request->rowOffset)->get();
                $totalCount = Booking::where('status', 'canceled')->whereBetween('booking_date', [$from, $to])->where('user_id', $userid)->count();
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
