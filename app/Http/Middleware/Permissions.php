<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Auth;

class Permissions
{

    public function handle($request, Closure $next,$perm)
    {
        $user = Auth::user();
        if ($user->is_admin == 1) {
            return $next($request);
        }
        else
        {
            if ($user->role_id != 0) {
                $role = $user->role_id;
                $usersPermission = Role::select('permissions')->where('id', $role)->first();

                $usersPermission = $usersPermission->permissions;

                $usersPermission = unserialize($usersPermission);


                if (array_search($perm, $usersPermission)===false) {
                    throw new \Exception("Permission is not available");

                } else {
                    return $next($request);
                }
            }
        }
    }
}
