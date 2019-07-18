<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleAssignController extends Controller
{

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'role_id' => 'required'
        ]);

        $roel_id_get = $request->role_id;

        $user = User::where('id', $id)->first();

        $user->role_id = $roel_id_get;

        $user->update();

    }

}
