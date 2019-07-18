<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable =['booking_id','currency_code','bill','method_id','transaction_id','created_by'];
}
