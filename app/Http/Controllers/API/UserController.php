<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Validator, Hash, Mail;
use App\Models\Role;
use DB;

class UserController extends Controller
{
    public $successStatus = 200;

    public function disableEnableUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->disabled = $request->disabled;

        if (!$user->update()) {

            return response()->json([
                'msg' => Lang::get('lang.error_update')], 404);
        }

        $response = [
            'msg' => Lang::get('lang.update_successful'),
            'user' => $user
        ];

        return response()->json($response, 200);
    }

    public function details()
    {
        $user = Auth::user();

        return response()->json(['success' => $user], $this->successStatus);
    }

    public function getUserList(Request $request)
    {
        if ($request->columnKey) $columnName = $request->columnKey;
        if ($request->rowLimit) $limit = $request->rowLimit;
        $offset = $request->rowOffset;
        $searchValue = $request->searchValue;

        if ($columnName == 'full_name') $columnName = 'first_name';
        else if ($columnName == 'role_title') $columnName = 'role_id';

        if ($searchValue) {
            $query = User::leftJoin('roles', 'users.role_id', '=', 'roles.id');
            $columns = ['first_name', 'last_name', 'email'];

            foreach ($columns as $column) {

                $query->orWhere($column, 'LIKE', '%' . $searchValue . '%')->where('is_admin', '=', 1)->get();
                $query->orWhere($column, 'LIKE', '%' . $searchValue . '%')->where('role_id', '!=', 0)->get();
            }

            $usersDetails = $query->where('is_admin', '=', 1)->where('role_id', '!=', 0)
                ->orderBy($columnName, $request->columnSortedBy)->take($limit)->skip($offset)
                ->select('users.id', 'users.first_name', 'users.last_name', DB::raw("CONCAT(users.first_name,' ',users.last_name) as full_name"), 'email', 'roles.title as role_title', 'users.is_admin', 'users.role_id', 'disabled');

            $count = $query->where('is_admin', '=', 1)->where('role_id', '!=', 0);

        } else {
            $usersDetails = User::leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->where('is_admin', 1)->orWhere('role_id', '!=', 0)->orderBy($columnName, $request->columnSortedBy)->take($limit)->skip($offset)
                ->select('users.id', 'users.first_name', 'users.last_name', DB::raw("CONCAT(users.first_name,' ',users.last_name) as full_name"), 'email', 'roles.title as role_title', 'users.is_admin', 'users.role_id', 'disabled');

            $count = User::where('is_admin', '!=', 0)->where('role_id', '!=', 0);

        }

        $users = $usersDetails->get();
        $totalCount = $count->count();

        return ['dataRows' => $users, 'count' => $totalCount];
    }

    public function userDetails($id)
    {
        $settingAll = new AllSettingsFormat;
        $user = User::select('first_name', 'last_name', 'email', 'phone', 'avatar')->find($id);
        $user->date_of_birth = $settingAll->getDate($user->date_of_birth);

        return view('users.view', ['user' => $user]);
    }

    public function updates(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255',
            'gender' => 'required',
            'role_id' => 'required'
        ]);

        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $gender = $request->input('gender');
        $role_id = $request->input('role_id');

        $user = User::with('schedules')->findOrFail($id);
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->gender = $gender;
        $user->role_id = $role_id;

        if (!$user->update()) {

            return response()->json([
                'msg' => Lang::get('lang.error_update')], 404);
        }

        $response = [
            'msg' => Lang::get('lang.update_successful'),
            'user' => $user
        ];

        return response()->json($response, 200);
    }

    public function delete($id)
    {
        $user = User::find($id);
        $schedules = $user->schedules;

        if (!$user->delete()) {

            foreach ($schedules() as $schedule) {

                $user->schedules()->attach($schedule);
            }

            return response()->json([
                'msg' => Lang::get('lang.delete_fail')], 404);
        }

        $response = [
            'msg' => Lang::get('lang.delete'),
        ];

        return response()->json($response, 404);
    }
}
