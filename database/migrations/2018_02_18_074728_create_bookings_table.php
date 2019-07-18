<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id');
            $table->integer('user_id');
            $table->string('phone_number')->nullable();
            $table->string('status')->default('pending');
            $table->date('booking_date');
            $table->string('booking_time');
            $table->integer('quantity')->default(1);
            $table->string('comment')->nullable();
            $table->double('booking_bill');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
