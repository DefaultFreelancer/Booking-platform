<?php

namespace App\Http\Controllers;

use App\Models\ClientSetting;
use App\Models\Setting;
use App\Models\Services;
use DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function homePage($serviceAlias = '')
    {
        $installCheck = config('gain.installed');

        if ($installCheck == true) {

            $signupCheck = (object)[
                'can_signup' => (int)Setting::select('setting_value')->where('setting_name', 'can_signup')->first()->setting_value,
                'can_login' => (int)Setting::select('setting_value')->where('setting_name', 'can_login')->first()->setting_value,
                'submit_booking_after_login' => (int)Setting::select('setting_value')->where('setting_name', 'submit_booking_after_login')->first()->setting_value,
            ];

            $user = Auth::user();

            if (Auth::check() && Auth::user()->is_admin == 1 || Auth::check() && Auth::user()->role_id != 0) {

                return redirect('/dashboard');
            }

            $landingPageMessage = Setting::where('setting_name', 'landing_page_message')->first()->setting_value;
            $landingPageHeader = Setting::where('setting_name', 'landing_page_header')->first()->setting_value;

            $service = 'false';
            if(!empty($serviceAlias)){
                $service = Services::where('alias',$serviceAlias)->first();
            }

            return view('welcome', [
                'signupCheck' => \GuzzleHttp\json_encode($signupCheck),
                'user' => $user,
                'service'=> $service,
                'landingPageMessage' => $landingPageMessage, 'landingPageHeader' => $landingPageHeader
            ]);

        } else {

            return redirect('/install');
        }

    }

}
