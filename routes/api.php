<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|   
*/

//auth protected route


Route::group([ 'prefix' => 'v1', 'middleware' => 'auth:api'], function(){
    Route::post('details', 'API\UserController@details');
    Route::post('logout','API\UserController@logout');
    Route::get('users', 'API\UserController@allUsers');
    Route::get('user/{id}', 'API\UserController@views');
    Route::post('user/{id}', 'API\UserController@delete');
    Route::put('user/{id}', 'API\UserController@updates');

    // Services Route
    Route::resource('service', 'API\ServiceController');
    // Services Route

    Route::resource('roles', 'API\RoleController');

    Route::resource('permissions', 'API\PermissionController');

    Route::resource('emailtemplates', 'API\EmailTemplateController');

    Route::resource('holidays', 'API\HolidayController');

    Route::get('emailtemplates', 'API\EmailTemplateController@showlist');
    Route::get('templatelist', 'API\EmailTemplateController@templatelist');

    //Route::get('service','API\ServiceController@index');

    Route::get('serviceshow','API\ServiceController@getdata');

    Route::post('invite', 'API\InviteController@process');


    Route::post('roleassign', 'API\RoleAssignController@update');

    Route::resource('profile', 'API\ProfileController');

    Route::get('/holidays', 'API\HolidayController@index');
    Route::post('/holiday/store', 'API\HolidayController@store');
    Route::post('/holidays/{id}', 'API\HolidayController@updateHolidays');
    Route::post('/delete/{id}', 'API\HolidayController@destroy');

    // Route::get('/offdaysetting', 'API\SettingController@createoffday');
    Route::post('/offdaysetting', 'API\SettingController@offdays');
    Route::get('/offdaysdata', 'API\SettingController@offdaysData');
    Route::get('/allroleid', 'API\InviteController@getRoleId');


});

Route::group(['prefix' => 'v1'], function() {
    //User Auth Route
    Route::post('login', 'API\UserController@login');
    Route::post('register', 'API\UserController@register');
    // Password Recover Route
    Route::post('recover', 'AuthController@recover');

    Route::post('accept/{token}', 'API\InviteController@invitedRegistration');

    Route::get('templatelist', 'API\EmailTemplateController@templatelist');

    Route::get('login', 'API\UserController@login');
});

Route::get('/getitemcode','API\LocalizationController@getCode');


