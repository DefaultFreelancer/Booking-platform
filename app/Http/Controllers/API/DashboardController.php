<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->is_admin == 0 && Auth::user()->role_id == 0) {
            return view('dashboard.clientdashboard');

        } else {

            return view('dashboard.dashboard');
        }
    }

    public function getData()
    {
        $day = date('d');
        $month = date("m");
        $nextMonth = (int)$month + 1;
        $lastMonth = (int)$month - 1;
        $year = date("Y");

        $totalAllBooking = Booking::whereBetween('booking_date', array($year . '-' . $month . '-' . $day, $year . '-' . $nextMonth . '-' . $day))->where('status', 'confirmed')->orWhere('status', 'pending')->count();
        $totalBooking = Booking::where('status', 'confirmed')->whereBetween('booking_date', array($year . '-' . $month . '-' . $day, $year . '-' . $nextMonth . '-' . $day))->count();
        $todaysBooking = Booking::whereDate('booking_date', date('Y-m-d'))->where('status', 'confirmed')->count();
        $curMonthTotBooking = Booking::where('status', 'confirmed')->whereBetween('booking_date', array($year . '-' . $month . '-' . 01, $year . '-' . $month . '-' . 31))->count();
        $lastMonthTotBooking = Booking::where('status', 'confirmed')->whereBetween('booking_date', array($year . '-' . $lastMonth . '-' . 01, $year . '-' . $lastMonth . '-' . 31))->count();
        $allTimeTotBooking = Booking::where('status', 'confirmed')->count();
        $todaysBookingPending = Booking::whereDate('booking_date', date('Y-m-d'))->where('status', 'pending')->count();

        //Line chart data
        $lineChartData = $this->lineChartData();

        //DoughnutData chart data
        $doughnutChartData = $this->doughnutChartData();

        return ['totalAllBooking' => $totalAllBooking,
            'totalBooking' => $totalBooking,
            'todaysBooking' => $todaysBooking,
            'todaysBookingPending' => $todaysBookingPending,
            'curMonthTotBooking' => $curMonthTotBooking,
            'lastMonthTotBooking' => $lastMonthTotBooking,
            'allTimeTotBooking' => $allTimeTotBooking,
            'lineChartData' => $lineChartData,
            'doughnutChartData' => $doughnutChartData
        ];
    }

    public function lineChartData()
    {
        $year = date("Y");

        $bookingData = Booking::groupBy(DB::raw('month(booking_date)'))
            ->whereBetween('booking_date', array($year . '-01-01', $year . '-12-31'))
            ->where('status', '=', 'confirmed')
            ->select(DB::raw('COUNT(booking_date) as count'), DB::raw('month(booking_date) as month'))
            ->get();

        $monthlyArray = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

        foreach ($bookingData as $data) {

            $monthlyArray[$data->month - 1] = $data->count;
        }

        return ['monthlyArray' => $monthlyArray];
    }

    public function doughnutChartData()
    {
        $date = Carbon::today()->toDateString();

        $confirmedCount = Booking::where('bookings.booking_date', '=', $date)
            ->where('status', '=', 'confirmed')
            ->count();

        $pendingCount = Booking::where('bookings.booking_date', '=', $date)
            ->where('status', '=', 'pending')
            ->count();

        return ['confirmedCount' => $confirmedCount, 'pendingCount' => $pendingCount];
    }
}
