<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable=['user_id','booking_id','total','due','created_by','comment','created_at','updated_at'];
}
