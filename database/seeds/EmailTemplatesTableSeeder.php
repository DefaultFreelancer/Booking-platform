<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Email Header
        $emailHeader = '<html>
                            <div style="width: 35%; color: #333333; font-family: Helvetica; margin:auto; padding-bottom: 10px;">
                                <div style="text-align:center; padding-top: 10px; padding-bottom: 10px;">
                                    <h1>{app_name}</h1>
                                </div>
                                <div style="padding: 35px;padding-left:20px; border-bottom: 1px solid #cccccc; border-top: 1px solid #cccccc;">';
        $emailFooter = '        </div>
                            </div>
                        </html>';
        
        DB::table("email_templates")->insert([
            'template_type' => 'user_invitation',
            'template_subject' => 'Invitation',
            'default_content' =>
                $emailHeader.'Hi,<br>
                The admin is inviting you to the Gain Booking
                Please click here on the link {verification_link} to accept!'.$emailFooter
        ]);

        DB::table("email_templates")->insert([
            'template_type' => 'user_registration',
            'template_subject' => 'Registration Confirmation',
            'default_content' =>
                        $emailHeader.'Hi {first_name},<br><br>

                        Your registration is completed.

                        Please click the link {verification_link} here to confirm email.'.$emailFooter
        ]);


        DB::table("email_templates")->insert([
            'template_type' => 'reset_password',
            'template_subject' => 'Reset Password',
            'default_content' =>
                        $emailHeader.'Hi,<br><br>

                        You requested to change your password

                        Please click here on the link {reset_password_link} to change your password!'.$emailFooter
        ]);

        DB::table("email_templates")->insert([
            'template_type' => 'booking_received',
            'template_subject' => 'Booking Received',
            'default_content' =>
                $emailHeader.'Hi {first_name},<br><br>

                        Your booking is received.

                        Please wait for Admins confirmation on your booking!

                        <h3>Booking Summery</h3>

                        <b>Booking ID:</b> {booking_id}<br>
                        <b>Service Name:</b> {service_title}<br>
                        <b>Quantity:</b> {booking_quantity}<br>
                        <b>Status:</b> {booking_status}<br>
                        <b>Date:</b> {booking_date}<br>
                        <b>Slot:</b> {booking_slot}<br>
                        <b>Payment:</b> {payment_status}'.$emailFooter
        ]);

        DB::table("email_templates")->insert([
            'template_type' => 'booking_confirmation',
            'template_subject' => 'Booking Confirmation',
            'default_content' =>
                $emailHeader.'Hi {first_name},<br><br>

                        Your booking has been confirmed.

                        <h3>Booking Summery</h3>

                        <b>Booking ID:</b> {booking_id}<br>
                        <b>Service Name:</b> {service_title}<br>
                        <b>Quantity:</b> {booking_quantity}<br>
                        <b>Status:</b> {booking_status}<br>
                        <b>Date:</b> {booking_date}<br>
                        <b>Slot:</b> {booking_slot}<br>
                        <b>Bill:</b> {bill}<br>
                        <b>Payment:</b> {payment_status}'.$emailFooter
        ]);

        DB::table("email_templates")->insert([
            'template_type' => 'booking_rejected',
            'template_subject' => 'Booking Rejected',
            'default_content' =>
                $emailHeader.'Hi {first_name},<br><br>

                        We are very sorry to inform you that your booking is Canceled due to some unavailable reasons.
                        Hoping you will be with us and we are bound to serve your satisfaction.

                        <h3>Booking Summery</h3>

                        <b>Booking ID:</b> {booking_id}<br>
                        <b>Service Name:</b> {service_title}<br>
                        <b>Status:</b> {booking_status}<br>
                        <b>Quantity:</b> {booking_quantity}<br>
                        <b>Date:</b> {booking_date}<br>
                        <b>Slot:</b> {booking_slot}'.$emailFooter
        ]);

    }
}
