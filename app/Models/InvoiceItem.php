<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable=['invoice_id','service_title','booking_date','booking_time','unit_price','quantity','total','created_at','updated_at'];
}
