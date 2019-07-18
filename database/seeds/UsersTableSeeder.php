<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     *
     * Insert demo data Users table
     */
    public function run()
    {
        // install for customer
        //$first_name = session('first_name');
        //$last_name = session('last_name');
        //$email = session('email');
        //$password = session('password');

        DB::table('users')->insert([
            'first_name' => 'Milivoje',
            'last_name' => 'Ivic',
            'email' => 'milivojeivic422@gmail.com',
            'phone' => '123456789',
            'password' => Hash::make('collection'),
            'verified' => 1,
            'is_admin' => 1,
            'token' => '',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
