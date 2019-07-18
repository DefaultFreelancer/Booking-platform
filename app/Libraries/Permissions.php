<?php

namespace App\Libraries;

use App\Models\Role;
use App\User;
use Illuminate\Support\Facades\Auth;

class Permissions
{
    protected $user;

    public function __construct()
    {

        $this->user = Auth::user() && Auth::user()->is_admin;

    }

    public function isAdmin()
    {
        if (Auth::user()) {
            $user_id = Auth::user()->id;
            $is_admin = $this->user;

            if ($is_admin == true) {

                return true;

            } else {

                return false;
            }

        } else {

            return false;
        }

    }

    public function hasPermission($per)
    {
        $role = Auth::user()->role_id;

        if ($role > 0) {
            $usersPermission = Role::select('permissions')->where('id', $role)->first();
            $usersPermission = $usersPermission->permissions;
            $usersPermission = unserialize($usersPermission);

            if (in_array($per, $usersPermission)) {

                return true;

            } else {

                return false;

            }

        } else {

            return false;
        }

    }
}