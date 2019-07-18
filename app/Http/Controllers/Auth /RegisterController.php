<?php

namespace App\Http\Controllers\Auth;

use App\Models\Invite;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Hash;
use Illuminate\Http\Request;
use Mail;
use App\Models\EmailTemplate;
use App\Libraries\Email;
use App\Models\Setting;

//use App\Notifications\UserRegisteredNotification;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function signup()
    {

        $clientSettings = Setting::select('setting_value')->where('setting_name', 'can_signup')->first();

        if (!$clientSettings || $clientSettings->setting_value != 1) {
            return redirect('/')->with('status', "registration_disabled");
        }

        return view('auth.register-client');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function update(array $data)
    {
        User::where('email', $data['email'])->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'password' => bcrypt($data['password']),
            //generates a random string that is 20 characters long
            'token' => str_random(25)
        ]);

        return User::where('email', $data['email'])->first();
    }

    protected function create(array $data)
    {
        $user = array(
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'token' => str_random(25)
        );

        if (Config::get('app.auto_validate_client_mail') == true) {
            $user['verified'] = 1;
        }

        return User::create($user);
    }


    protected function register(Request $request)
    {

        $input = $request->all();
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if (User::where('email', $input['email'])->exists()) {

            if (User::select('password')->where('email', $input['email'])->first()->password == '' || User::select('password')->where('email', $input['email'])->first()->password == null) {
                $data = $this->update($input)->toArray();

            } else {
                $this->validate($request, [
                    'email' => 'required|string|email|max:255|unique:users'
                ]);
            }

        } else $data = $this->create($input)->toArray();

        $data['token'] = str_random(25);

        $user = User::find($data['id']);
        $user->token = $data['token'];
        $user->save();

        $content = EmailTemplate::select('template_subject', 'default_content', 'custom_content')->where('template_type', 'user_registration')->first();
        $clientCanLogin = Setting::select('setting_value')->where('setting_name', 'can_login')->first()->setting_value;

        if ($content) {
            if ($content->custom_content) $text = $content->custom_content;
            else $text = $content->default_content;
            $subject = $content->template_subject;
            $appName = Setting::select('setting_value')->where('setting_name', 'email_from_name')->first()->setting_value;
            $first_name = $request->input('first_name');
            $last_name = $request->input('last_name');
            $appurl = \Request::root();
            $verification_link = $appurl . "/verify/" . $data['token'];
            $mailText = str_replace('{first_name}', $first_name, str_replace('{last_name}', $last_name, str_replace('{verification_link}', $verification_link, str_replace('{app_name}', $appName, $text))));
            $email = $request->input('email');
            $emailSend = new Email;
            $emailSend->sendEmail($mailText, $email, $subject);

            if ($clientCanLogin == 1) {
                return redirect(route('login'))->with('status', Lang::get('lang.confirmation_email_send'));
            } else {
                return redirect('/')->with('status', "mail_sent");
            }
        }

        if ($clientCanLogin == 1) {

            return redirect(route('login'))->with('status', Lang::get('lang.something_wrong'));

        } else {

            return redirect('/')->with('status', "something_wrong");
        }
    }

    public function verifyUser($token)
    {
        $user = User::where('token', $token)->first();

        if (!is_null($user)) {
            $user->verified = 1;
            $user->token = '';
            $user->save();
            $clientCanLogin = Setting::select('setting_value')->where('setting_name', 'can_login')->first()->setting_value;

            if ($clientCanLogin == 1) {

                return redirect(route('login'))->with('status', Lang::get('lang.activation_completed'));

            } else {

                return redirect('/')->with('status', Lang::get('lang.activation_completed'));
            }

        }

        return redirect('/')->with('status', Lang::get('lang.something_wrong'));
    }


    public function regForm($token)
    {
        $invitedUser = Invite::select('email', 'invited_as')->where('token', $token)->first();
        $email = $invitedUser->email;

        return view('auth/register', ['email' => $email, 'token' => $token]);
    }

    public function invitedRegistration(Request $request, $token)
    {

        $invitedUser = Invite::select('email', 'invited_as')->where('token', $token)->first();
        $email = $invitedUser->email;
        $role_id = $invitedUser->invited_as;

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $password = $request->input('password');

        if (User::select('email')->where('email', $email)->exists()) {

            if (User::where('email', $email)->update(['first_name' => $first_name, 'last_name' => $last_name, 'token' => $token, 'password' => Hash::make($password), 'verified' => 1, 'role_id' => $role_id])) {
                Invite::where('email', $email)->update(['token' => '']);
            }

        } else {

            if (User::create(['first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'token' => $token, 'password' => Hash::make($password), 'verified' => 1, 'role_id' => $role_id])) {
                Invite::where('email', $email)->update(['token' => '']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => Lang::get('lang.registration_done'),
            'users' => $email
        ]);
    }


}
