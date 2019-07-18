<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $uploads = '/uploads/profiles';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'password', 'gender', 'cellphone', 'avatar', 'date_of_birth', 'verified', 'role_id','disabled', 'token', 'is_admin', 'notification_check'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function AauthAcessToken()
    {
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function getFileAttribute($avatar)
    {
        return $this->uploads . $avatar;
    }

}
