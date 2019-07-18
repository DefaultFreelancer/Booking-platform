<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\Models\Booking;
use App\Models\Services;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function getReport($id, $bDate)
    {
        $allSet = new AllSettingsFormat;

        $data = Services::where('id', $id)->first();

        $stackSlot = array();
        $bookedSlot = array();
        $timeSlotBooked = array();
        $bookedSeatConfirm = array();
        $bookedSeatPending = array();
        $confirmSeat = array();
        $pendingSeat = array();
        $availableSeat = array();

        $seat = Booking::select('booking_time', 'quantity', 'status')->where(['service_id' => $id, 'booking_date' => $bDate])->where('status', '!=', 'canceled')->get();

        foreach ($seat as $s) {

            array_push($bookedSlot, unserialize($s->booking_time));

            if ($s->status == 'confirmed') {
                array_push($bookedSeatConfirm, $s->quantity);
                array_push($bookedSeatPending, 0);
            }

            if ($s->status == 'pending') {
                array_push($bookedSeatPending, $s->quantity);
                array_push($bookedSeatConfirm, 0);
            }
        }

        $count = Booking::select('booking_time', 'quantity', 'status')->where(['service_id' => $id, 'booking_date' => $bDate])->where('status', '!=', 'canceled')->count();

        if ($data->service_duration_type == 'hourly') {

            for ($time = $data->service_starts; $time < $data->service_ends; $time = date("H:i:s", (strtotime($time) - strtotime("00:00:00")) + strtotime($data->service_duration))) {

                $finalSeatConfirm = 0;
                $finalSeatPending = 0;
                $finalSeat = 0;

                array_push($stackSlot, $allSet->timeFormat($time));

                foreach ($bookedSlot as $index => $bs) {

                    if (in_array($time, $bs)) {
                        $finalSeatConfirm += $bookedSeatConfirm[$index];
                        $finalSeatPending += $bookedSeatPending[$index];
                        $finalSeat += $finalSeatConfirm[$index] + $finalSeatPending;

                    } else {
                        $finalSeatConfirm += 0;
                        $finalSeatPending += 0;
                    }
                }

                array_push($confirmSeat, $finalSeatConfirm);
                array_push($pendingSeat, $finalSeatPending);
                array_push($availableSeat, $data->available_seat - $finalSeat);
            }

            return ['stack' => $stackSlot, 'confirmSeat' => $confirmSeat, 'pendingSeat' => $pendingSeat, 'availableSeat' => $availableSeat, 'count' => $count];
        }

        if ($data->service_duration_type == 'daily') {
            $countedSeatConfirmed = 0;
            $countedSeatPending = 0;

            foreach ($seat as $s) {

                if ($s->status == 'confirmed') {
                    $countedSeatConfirmed = $countedSeatConfirmed + (int)$s->quantity;
                }

                if ($s->status == 'pending') {
                    $countedSeatPending = $countedSeatPending + (int)$s->quantity;
                }
            }

            return ['stack' => [$allSet->getDate($bDate)], 'confirmSeat' => [$countedSeatConfirmed], 'pendingSeat' => [$countedSeatPending], 'availableSeat' => $availableSeat, 'count' => $count];
        }
    }
}
