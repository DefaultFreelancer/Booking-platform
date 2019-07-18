<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable=[
        'service_id',
        'user_id',
        'phone_number',
        'status',
        'booking_date',
        'booking_time',
        'quantity',
        'comment',
        'booking_bill'
    ];
}
