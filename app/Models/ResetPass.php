<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPass extends Model
{
    protected $fillable =['email','token'];
}
