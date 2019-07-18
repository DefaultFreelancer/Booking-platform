<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\ClientSetting;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Validation\ValidationException;
use Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => [
                'required', 'string',
                Rule::exists('users')->where(function ($query) {
                    $query->where('verified', true);
                })
            ],
            'password' => 'required|string',
        ], [
            $this->username() . '.exists' => Lang::get('lang.inactive_invalid_email')
        ]);
    }

    public function userlogout()
    {
        Auth::guard('web')->logout();

        return redirect('/');
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $userPassword = Hash::make($request->password);
        $user = User::where('email', $request->email)->first();

        // Check if user is 'client' and have permission to login
        if ($user && Hash::check($request->password, $user->password)) {

            if ($user->is_admin == 0) {

                if ($user->role_id == 0) {
                    $appPermission = Setting::select('setting_value')->where('setting_name', 'can_login')->first();

                    if (!$appPermission || $appPermission->setting_value != 1) {
                        throw ValidationException::withMessages([
                            $this->username() => Lang::get('lang.have_not_permission_to_login'),
                        ]);
                    }

                } else {

                    if ($user->disabled == 1) {
                        throw ValidationException::withMessages([
                            $this->username() => Lang::get('lang.have_not_permission_to_login'),
                        ]);
                    }
                }
            }
        }

        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

}