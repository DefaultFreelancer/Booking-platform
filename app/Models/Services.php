<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $fillable = [
        'title',
        'price',
        'allow_cancel',
        'auto_confirm',
        'allow_booking_max_day_ago',
        'service_duration_type',
        'service_starting_date',
        'service_ending_date',
        'service_duration',
        'multiple_bookings',
        'available_seat',
        'description',
        'service_starts',
        'service_ends',
        'max_booking',
        'alias',
        'created_by',
        'activation',
    ];
}
