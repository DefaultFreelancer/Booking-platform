<?php

namespace App\Http\Controllers\API;

use App\Libraries\Email;
use App\Libraries\Permissions;
use App\Models\EmailTemplate;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\Role;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Validator, Hash;
use Config;

class InviteController extends Controller
{

    public function getRoleId()
    {
        $data = Role::all();

        return $data;
    }

    public function permissionCheck()
    {
        $controller = new Permissions;

        return $controller;
    }

    public function process(Request $request)
    {
        if ($this->permissionCheck()->isAdmin()) {

            do {
                $token = str_random();
            } while (Invite::where('token', $token)->first());


            $invite = Invite::create([
                'email' => $request->get('email'),
                'invited_as' => $request->get('invited_as'),
                'token' => $token
            ]);

            $content = EmailTemplate::select('template_subject', 'default_content', 'custom_content')->where('template_type', 'user_invitation')->first();
            $subject = $content->template_subject;


            if ($content->custom_content) {
                $text = $content->custom_content;

            } else {
                $text = $content->default_content;
            }

            $link = $this->appUrl . '/accept/' . $token;
            $appName = Setting::select('setting_value')->where('setting_name', 'email_from_name')->first()->setting_value;
            $invited_by = Auth::user()->first_name . " " . Auth::user()->last_name;
            $mailText = str_replace('{verification_link}', $link, str_replace('{app_name}', $appName, str_replace('{invited_by}', $invited_by, $text)));
            $email = $request->input('email');

            $emailSend = new Email;
            if (!$emailSend->sendEmail($mailText, $email, $subject)) {

                return response()->json(Lang::get('lang.email_setting'), 401);
            }

        } else {
            $response = [
                'msg' => Lang::get('lang.permission_error'),
                'template' => Lang::get('lang.permission_is_not_available')
            ];

            return response()->json($response, 401);
        }

    }

    public function accept($token)
    {
        $invite = Invite::where('token', $token)->first();

        if (!is_null($invite)) {
            $invite->is_accepted = 1;

            $invite->save();

            $email = $invite->email;
            $role_id = $invite->invited_as;

            return redirect('register/' . $token);
        }
    }
}