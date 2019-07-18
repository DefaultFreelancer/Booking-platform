<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * Settings table default values seed
     */
    public function run()
    {
        $purchase_code = session('purchase_code');
        DB::table('settings')->insert([
            'setting_name' => 'time_format',
            'setting_value' => '12h',

        ]);

        DB::table('settings')->insert([
            'setting_name' => 'date_format',
            'setting_value' => 'Y-m-d',

        ]);

        DB::table('settings')->insert([
            'setting_name' => 'currency_symbol',
            'setting_value' => '$',

        ]);

        DB::table('settings')->insert([
            'setting_name' => 'currency_format',
            'setting_value' => 'left',

        ]);

        DB::table('settings')->insert([
            'setting_name' => 'thousand_separator',
            'setting_value' => ',',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'language_setting',
            'setting_value' => 'english',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'decimal_separator',
            'setting_value' => '.',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'number_of_decimal',
            'setting_value' => '2',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'offday_setting',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'email_from_name',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'email_from_address',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'email_driver',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'email_smtp_host',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'email_port',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'email_smtp_password',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'email_encryption_type',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'max_row_per_table',
            'setting_value' => '10',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'app_name',
            'setting_value' => 'Gain Booking',
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'app_logo',
            'setting_value' => 'default-logo.png',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'currency_code',
            'setting_value' => 'USD',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'purchase_code',
            'setting_value'   => 'test_code'
        ]);

        DB::table('settings')->insert([
            'setting_name' => 'can_signup',
            'setting_value' => 1,
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'can_login',
            'setting_value' => 1,
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'submit_booking_after_login',
            'setting_value' => 0,
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'landing_page_message',
            'setting_value' => '',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'landing_page_header',
            'setting_value' => '',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'mandrill_api',
            'setting_value' => '',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'sparkpost_api',
            'setting_value' => '',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'mailgun_domain',
            'setting_value' => '',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'mailgun_api',
            'setting_value' => '',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'invoice_logo',
            'setting_value' => 'invoice.jpg',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'company_name',
            'setting_value' => 'Demo Company Name',
        ]);
        DB::table('settings')->insert([
            'setting_name' => 'company_info',
            'setting_value' => 'Some company address here',
        ]);
    }
}
