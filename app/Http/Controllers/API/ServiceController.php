<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\Libraries\Permissions;
use App\Models\Booking;
use App\Models\Services;
use App\Models\Setting;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;


class ServiceController extends Controller
{
    public function activeData()
    {
        $data = Services::all()->where('activation', 1);

        return $data;

    }

    public function services()
    {
        return view('services.index');
    }

    public function permissionCheck()
    {
        $controller = new Permissions;

        return $controller;
    }

    public function getData()
    {
        $data = Services::all();
        $data = $this->makeDataReadAble($data);

        return $data;
    }

    public function getSortedData(Request $request)
    {
        if ($request->columnKey) $columnName = $request->columnKey;
        if ($request->rowLimit) $limit = $request->rowLimit;
        $offset = $request->rowOffset;

        $data = Services::orderBy($columnName, $request->columnSortedBy)->take($limit)->skip($offset)->get();
        $data = $this->makeDataReadAble($data);
        $totalCount = Services::count();

        return ['dataRows' => $data, 'count' => $totalCount];
    }

    public function makeDataReadAble($data)
    {
        $settingApp = new AllSettingsFormat;

        foreach ($data as $dt) {

            $dt->service_starts = $settingApp->timeFormat($dt->service_starts);
            $dt->service_ends = $settingApp->timeFormat($dt->service_ends);

            if ($dt->multiple_bookings == 1) {
                $dt->multiple_bookings = Lang::get('lang.yes');

            } else {
                $dt->multiple_bookings = Lang::get('lang.no');
            }

            if (Booking::where('service_id', $dt->id)->count()) {
                $dt->used = 1;

            } else {
                $dt->used = 0;
            }

            $dt->price = $settingApp->getCurrency($settingApp->thousandSep($dt->price));
        }

        return $data;
    }

    public function getServiceAndOffDays()
    {
        $allSet = new AllSettingsFormat;

        $data = Services::where('activation', 1)
            ->where(function ($data) {
                $data->whereRaw('service_starting_date <= DATE_ADD(curdate(), INTERVAL allow_booking_max_day_ago DAY)')
                    ->orWhereNull('service_starting_date');
            })->get();

        $offDays = Setting::Select('setting_value')->where('setting_name', 'offday_setting')->first();

        if (!$offDays) {
            $offDays = [0];

        } else {
            $offDays = array_map('intval', explode(',', $offDays->setting_value));
        }

        $holydays = Holiday::Select('start_date', 'end_date')->get();

        foreach ($data as $d) {

            $d->service_price = $d->price;
            $d->price = $allSet->getCurrency($d->price);
        }

        return ['serviceData' => $data, 'offdays' => $offDays, 'holydays' => $holydays];

    }

    public function getTiming($id, $bDate)
    {
        $allSet = new AllSettingsFormat;

        $data = Services::where('id', $id)->first();
        $stackSlot = array();
        $bookedSlot = array();
        $timeSlotBooked = array();
        $bookedSeat = array();
        $availableSeat = array();
        $combineSlotSeat = array_combine($bookedSlot, $bookedSeat);

        $seat = Booking::select('booking_time', 'quantity')->where(['service_id' => $id, 'booking_date' => $bDate])->where('status', '!=', 'canceled')->get();

        if ($data->service_duration_type == 'hourly') {
            foreach ($seat as $s) {

                array_push($bookedSlot, unserialize($s->booking_time));
                array_push($bookedSeat, $s->quantity);
            }

            for ($time = $data->service_starts; $time < $data->service_ends;) {
                $finalSeat = 0;
                array_push($stackSlot, $allSet->timeFormat($time));

                foreach ($bookedSlot as $index => $bs) {

                    if (in_array($time, $bs)) {
                        $finalSeat += $bookedSeat[$index];

                    } else {
                        $finalSeat += 0;
                    }
                }

                array_push($availableSeat, $data->available_seat - $finalSeat);

                $maxTime = "23.59.59";
                $calculatedTime = strtotime($time) - strtotime("00:00:00") + strtotime($data->service_duration);

                if ($calculatedTime > strtotime($maxTime)) {
                    break;

                } else {
                    $time = date("H:i:s", $calculatedTime);
                }
            }

            return ['stack' => $stackSlot, 'seat' => $availableSeat];
        }

        if ($data->service_duration_type == 'daily') {
            $countedSeat = 0;

            foreach ($seat as $s) {

                $countedSeat = $countedSeat + (int)$s->quantity;
            }

            return ['stack' => [$allSet->getDate($bDate)], 'seat' => [$data->available_seat - $countedSeat]];
        }
    }

    public function getTimingZero($id)
    {
        return ['stack' => 0, 'seat' => 0];
    }

    public function store(Request $request)
    {
        $allSet = new AllSettingsFormat;

        $service = $this->validate($request, [
            'title' => 'required',
            'price' => 'required',
        ]);

        $title = $request->input('title');
        $price = $request->input('price');
        $service_duration_type = $request->service_duration_type;
        $service_starting_date = $request->service_starting_date;
        $service_ending_date = $request->service_ending_date;

        if ($request->service_starting_date == 'Invalid date' || $request->service_starting_date == '') {
            $service_starting_date = null;
        }

        if ($request->service_ending_date == 'Invalid date' || $request->service_ending_date == '') {
            $service_ending_date = null;
        }

        $service_starts = $allSet->setTimeFormat($request->input('service_starts'));
        $service_ends = $allSet->setTimeFormat($request->input('service_ends'));
        $service_duration = $request->input('service_duration');
        $multiple_bookings = $request->input('multiple_bookings');
        $available_seat = $request->available_seat;
        $description = $request->input('description');
        $max_booking = $request->input('max_booking');
        $created_by = Auth::user()->id;

        $serviceDetails = ['title' => $title,
            'price' => $price,
            'multiple_bookings' => $multiple_bookings,
            'description' => $description,
            'service_duration_type' => $service_duration_type,
            'service_starting_date' => $service_starting_date,
            'service_ending_date' => $service_ending_date,
            'service_starts' => $service_starts,
            'service_ends' => $service_ends,
            'service_duration' => $service_duration,
            'max_booking' => $max_booking,
            'activation' => 1,
            'created_by' => $created_by,
            'available_seat' => $available_seat];

        $alias = str_replace(' ', '-', strtolower($title));
        $countAlias = Services::where('alias', $alias)->count();

        if ($countAlias > 0) {
            $maxId = Services::max('id') + 1;
            $serviceDetails['alias'] = $alias . '-' . $maxId;

        } else {
            $serviceDetails['alias'] = $alias;
        }

        if ($service = Services::create($serviceDetails)) {

            $response = [
                'msg' => Lang::get('lang.created_successfully'),
                'Service' => $service
            ];

            return response()->json($response, 201);
        }

        $response = [
            'msg' => Lang::get('lang.error_during_creating')
        ];

        return response()->json($response, 404);
    }

    public function show($id)
    {
        $allSet = new AllSettingsFormat;
        $data = Services::find($id);
        $data->service_starts = $allSet->timeFormat($data->service_starts);
        $data->service_ends = $allSet->timeFormat($data->service_ends);

        return $data;
    }

    public function deactivate(Request $request, $id)
    {
        $service = Services::find($id);
        $service->update($request->all());

        if ($service) {
            $response = [
                'msg' => Lang::get('lang.successfully_updated'),
                'Service' => $service
            ];

            return response()->json($response, 201);
        }

        $response = [
            'msg' => Lang::get('lang.error_during_update')
        ];

        return response()->json($response, 404);
    }

    public function update(Request $request, $id)
    {
        $allSet = new AllSettingsFormat;
        $service = Services::find($id);

        if ($request->service_starting_date && $request->service_starting_date) {

            if ($request->service_starting_date == 'Invalid date' || $request->service_starting_date == '') {
                $request['service_starting_date'] = null;
            }

            if ($request->service_ending_date == 'Invalid date' || $request->service_ending_date == '') {
                $request['service_ending_date'] = null;
            }
        }

        if ($request->service_starts && $request->service_ends) {
            $request['service_starts'] = $allSet->setTimeFormat($request->service_starts);
            $request['service_ends'] = $allSet->setTimeFormat($request->service_ends);
        }

        $service->update($request->all());

        if ($service) {
            $response = [
                'msg' => Lang::get('lang.successfully_updated'),
                'Service' => $service
            ];

            return response()->json($response, 201);
        }

        $response = [
            'msg' => Lang::get('lang.error_during_update')
        ];

        return response()->json($response, 404);
    }

    public function delete($id)
    {
        Services::where('id', $id)->delete();
    }

    public function serviceSetting(Request $request, $id)
    {

        $setting = ['allow_cancel' => $request->allow_cancel,
            'auto_confirm' => $request->auto_confirm,
            'activation' => $request->activation,
            'allow_booking_max_day_ago' => $request->allow_booking_max_day_ago];

        $alias = str_replace(' ', '-', strtolower($request->alias));
        $countAlias = Services::where('alias', $alias)->where('id', '!=', $id)->count();
        $service = Services::find($id);

        if ($countAlias == 0) {
            $setting['alias'] = $alias;

            $response = [
                'msg' => Lang::get('lang.successfully_updated'),
                'Service' => $service
            ];

        } else {

            $response = [
                'msg' => Lang::get('lang.successfully_updated') . ' ' . Lang::get('lang.and_external_link_already_exists'),
                'Service' => $service
            ];
        }

        $service->update($setting);

        return response()->json($response, 201);
    }

    public function getServiceAlias($alias)
    {
        $service = Services::where('alias',$alias)->first();
    }

}
