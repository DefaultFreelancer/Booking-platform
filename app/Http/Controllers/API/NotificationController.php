<?php

namespace App\Http\Controllers\API;


use App\Libraries\AllSettingsFormat;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class NotificationController extends Controller
{

    public function index()
    {
        $allSet = new AllSettingsFormat;
        $user = Auth::user()->id;
        $notify = Notification::orderBy('id', 'DESC')->join('users', 'notifications.activity_id', '=', 'users.id')->select('notifications.*', 'users.avatar', 'users.first_name', 'users.last_name')->get();
        $myNotifications = array();

        foreach ($notify as $key => $noti) {

            $notifyToArray = array_map('intval', explode(',', $noti->notify_to));

            if (!in_array($user, $notifyToArray)) {
                unset($notify[$key]);
            }
        }

        $count = 0;
        $notifcations = Notification::all();

        $notification_check_time = User::select('notification_check')->where('id', Auth::user()->id)->first();

        foreach ($notifcations as $noti) {

            if (!in_array(Auth::user()->id, explode(',', $noti->read_by)) && in_array(Auth::user()->id, explode(',', $noti->notify_to)) && $notification_check_time->notification_check < $noti->updated_at) {
                $count++;
            }
        }

        return ['notify' => $notify, 'count' => $count];
    }

    public function singleView($id)
    {
        $allSet = new AllSettingsFormat;

        $booking_id = Notification::find($id);
        $data = Booking::find($booking_id->booking_id);

        $data->notiTitle = Notification::select('event')->where('id', $id)->first()->event;

        $data->service_id = Services::select('title')->where('id', $data->service_id)->first()->title;

        $data->user_id = User::select('email')->where('id', $data->user_id)->first()->email;

        $data->booking_date = $allSet->getDate($data->booking_date);

        $data->booking_time = $allSet->timeFormat(unserialize($data->booking_time));

        return view('notification.notificationSingleView', ['data' => $data]);
    }

    public function allNotification()
    {
        $data = $this->index();

        return view('notification.allNotification', ['data_notify' => $data['notify']]);
    }

    public function update(Request $request, $id)
    {

        $notifcations = Notification::find($id);

        if (!in_array(Auth::user()->id, explode(',', $notifcations->read_by))) {
            $notifcations->read_by = $notifcations->read_by . ',' . $request->read_by;


            $notifcations->save();

            return response()->json(['success' => true, 'success' => Lang::get('lang.notification_open')]);

        } else {

            return response()->json(['success' => false, 'errors' => Lang::get('lang.error')]);
        }
    }

    public function countUp($id)
    {
        $time = date('Y-m-d H:i:s');
        User::where('id', $id)->update(['notification_check' => $time]);

    }
}
