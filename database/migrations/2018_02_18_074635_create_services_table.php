<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->double('price');
            $table->boolean('allow_cancel')->default(0); //Allow user to cancel "confirmed","paid" bookings
            $table->boolean('auto_confirm')->default(0);//Allow user to cancel "confirmed","paid" bookings
            $table->integer('allow_booking_max_day_ago')->default(60);
            $table->time('service_duration')->nullable();
            $table->boolean('multiple_bookings')->default(0); //Allow multiple bookings per service duration
            $table->integer('available_seat')->default(0); //Show spaces left (interval booking)
            $table->longText('description')->nullable();
            $table->string('service_duration_type');
            $table->time('service_starts')->nullable();
            $table->time('service_ends')->nullable();
            $table->date('service_starting_date')->nullable();
            $table->date('service_ending_date')->nullable();
            $table->integer('max_booking');
            $table->string('alias')->nullable();
            $table->integer('created_by');
            $table->boolean('activation')->default(0);
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
        Schema::dropIfExists('services');
    }
}
