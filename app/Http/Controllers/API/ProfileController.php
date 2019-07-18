<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;


class ProfileController extends Controller
{
    public function myProfile()
    {
        $allSetting = new AllSettingsFormat;
        $user = Auth::user();

        $dateformat = $allSetting->getDateFormat();
        $user->date_of_birth = $allSetting->getDate($user->date_of_birth);

        return view('users.profile', ['profile' => $user]);
    }

    public function index()
    {
        $allseting = new AllSettingsFormat;
        $user = Auth::user();
        $user->date_of_birth = $allseting->getDate($user->date_of_birth);

        return response()->json([
            'profile' => $user,
        ], 200);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
        ]);

        $user_id = Auth::user()->id;
        $avatar = '';

        if ($request->avatar == Auth::user()->avatar) {
            $avatar = Auth::user()->avatar;

        } else {

            if ($file = $request->avatar) {
                list($type, $file) = explode(';', $request->avatar);
                list(, $extension) = explode('/', $type);
                $fileName = uniqid() . '.' . $extension;
                $source = fopen($request->avatar, 'r');
                $destination = fopen(public_path() . '/uploads/profile/' . $fileName, 'w');
                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);

                $avatar = $fileName;
            }

            if (Auth::user()->avatar != 'default.jpg' && Auth::user()->avatar != '' && file_exists(public_path() . '/uploads/profile/' . Auth::user()->avatar)) {
                unlink(public_path() . '/uploads/profile/' . Auth::user()->avatar);
            }
        }

        User::where('id', $user_id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'avatar' => $avatar
        ]);

        return response()
            ->json([
                'saved' => true
            ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => Lang::get('lang.password_updated')]);

    }
}
