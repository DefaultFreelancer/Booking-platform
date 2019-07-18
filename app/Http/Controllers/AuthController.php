<?php

namespace App\Http\Controllers;

use App\Libraries\Email;
use App\Models\EmailTemplate;
use App\Models\ResetPass;
use Illuminate\Http\Request;
use DB, Hash;
use App\User;
use Illuminate\Support\Facades\Lang;
use App\Models\Setting;

class AuthController extends Controller
{
    public function verifyUser($token)
    {
        $check = DB::table('users')->where('token', $token)->first();

        if (!is_null($check)) {
            $user = User::find($check->id);

            if ($user->verify == 1) {

                return response()->json([
                    'success' => true,
                    'message' => Lang::get('lang.account_already_verified')
                ]);
            }

            $user->update(['verify' => 1]);
            DB::table('users')->where('token', $token)->delete();

            return response()->json([
                'success' => true,
                'message' => Lang::get('lang.successfully_verified')
            ]);
        }

        return response()->json(['success' => false, 'error' => Lang::get('lang.verification_code_invalid')]);
    }

    public function recover(Request $request)
    {
        $this->validate($request, [
            "email" => "required",
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $error_message = Lang::get('lang.email_not_found');

            return response()->json(['success' => false, 'errors' => ['email' => $error_message]], 401);
        }
        try {

            do {
                $token = str_random();
            } while (ResetPass::where('token', $token)->first());

            $invite = ResetPass::create([
                'email' => $request->get('email'),
                'token' => $token
            ]);

            $email = $request->get('email');

            $content = EmailTemplate::select('template_subject', 'default_content', 'custom_content')->where('template_type', 'reset_password')->first();
            $subject = $content->template_subject;

            if ($content->custom_content) {
                $text = $content->custom_content;

            } else {
                $text = $content->default_content;
            }

            $link = $this->appUrl . '/password/reset/' . $token;
            $appName = Setting::select('setting_value')->where('setting_name', 'email_from_name')->first()->setting_value;
            $mailText = str_replace('{reset_password_link}', $link, str_replace('{app_name}', $appName, $text));

            $email = $request->input('email');

            $emailSend = new Email;
            $emailSend->sendEmail($mailText, $email, $subject);

        } catch (\Exception $e) {
            $error_message = $e->getMessage();

            return response()->json(['success' => false, 'errors' => $error_message], 401);
        }

        return response()->json([
            'success' => true, 'data' => ['message' => Lang::get('lang.reset_email_send')]
        ]);
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required |confirmed | min:6',
            'password_confirmation' => 'required| min:6 ',
        ]);

        $email = $request->email;
        $password = $request->password;
        $token = $request->token;

        $emailForPass = ResetPass::select('email')->where('token', $token)->first()->email;

        if ($emailForPass == $email) {
            $user = User::where('email', $email)->first();
            $user->password = Hash::make($password);
            $user->save();

            ResetPass::where('email', $email)->delete();
        }
    }

}
