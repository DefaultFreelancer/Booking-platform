<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@homePage')->name('welcome');
Route::get('services/{alias}', 'HomeController@homePage');

Route::get('/login', '\App\Http\Controllers\Auth\LoginController@showLoginForm');
Route::post('/login', '\App\Http\Controllers\Auth\LoginController@login');

// Auth Route
Route::post('login', [ 'as' => 'login', 'uses' =>  '\App\Http\Controllers\Auth\LoginController@login']);
Route::post('/recover', 'AuthController@recover');

Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequest');
Route::post('/recoverpass', 'AuthController@recover');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@resetForm');

Route::post('/password/reset', 'AuthController@reset');

Route::get('/verify/{token}', 'Auth\RegisterController@verifyUser');
Route::get('accept/{token}', 'API\InviteController@accept');
Route::get('register/{token}', 'Auth\RegisterController@regForm');
Route::post('register/{token}', 'Auth\RegisterController@invitedRegistration');
Route::get('register', 'Auth\RegisterController@signup');
Route::post('registerclient', 'Auth\RegisterController@register');
Route::post('/setcookie', 'HomeController@setCookie');

// Auth middleware group Route
Route::group(['middleware' => 'auth'], function () {
    
    Route::get('/dashboard', 'API\DashboardController@index')->name('dashboard');
    Route::get('/dashboarddata', 'API\DashboardController@getData');

    // Profile Route
    Route::get('myprofile', 'API\ProfileController@myProfile');
    Route::get('user-profile', 'API\ProfileController@index');
    Route::post('profile/{id}', 'API\ProfileController@update');
    Route::post('/updatePassword/{id}', 'API\ProfileController@updatepassword');
    Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

    // Email template Route
    Route::post('templatelist', 'API\EmailTemplateController@templateList');
    Route::get('/gettemplatecontent/{id}', 'API\EmailTemplateController@show')->middleware('permissions:can_edit_email_template');
    Route::post('/setcustomcontent/{id}', 'API\EmailTemplateController@update')->middleware('permissions:can_edit_email_template');

    // Email Setting Route
    Route::post('/emailsetting', 'API\SettingController@emailSettingSave')->middleware('permissions:can_edit_email_setting');
    Route::get('/emailsettingdata', 'API\SettingController@emailSettingData')->middleware('permissions:can_edit_email_setting');
    Route::post('/testmail', 'API\SettingController@testMail');

    // Service Route
    Route::post('/serviceid/{id}', 'API\ServiceController@update')->middleware('permissions:can_add_service');
    Route::post('/serviceSetting/{id}', 'API\ServiceController@serviceSetting')->middleware('permissions:can_add_service');
    Route::post('/servicedeactive/{id}', 'API\ServiceController@deactivate')->middleware('permissions:can_add_service');
    Route::post('/servicedefault/{id}', 'API\ServiceController@setdefault')->middleware('permissions:can_add_service');
    Route::get('serviceshow', 'API\ServiceController@getData')->middleware('permissions:can_add_service');
    Route::post('serviceshow', 'API\ServiceController@getSortedData')->middleware('permissions:can_add_service');
    Route::post('/deleteservice/{id}', 'API\ServiceController@delete')->middleware('permissions:can_add_service');
    Route::get('servicenew/create', 'API\ServiceController@create')->middleware('permissions:can_add_service');
    Route::post('servicenew/create/store', 'API\ServiceController@store')->middleware('permissions:can_add_service');

    // Off Day Setting Route
    Route::post('/offdaysetting', 'API\SettingController@offdays')->middleware('permissions:can_edit_off_day_setting');
    Route::get('/offdaysdata', 'API\SettingController@offdaysData')->middleware('permissions:can_edit_off_day_setting');

    // Holiday Route
    Route::get('/holidays', 'API\HolidayController@index')->middleware('permissions:can_edit_holi_day_setting');
    Route::post('/holiday/store', 'API\HolidayController@store')->middleware('permissions:can_edit_holi_day_setting');
    Route::get('/holidays/{id}', 'API\HolidayController@show')->middleware('permissions:can_edit_holi_day_setting');
    Route::post('/holidays/{id}', 'API\HolidayController@updateHolidays')->middleware('permissions:can_edit_holi_day_setting');
    Route::post('/delete/{id}', 'API\HolidayController@destroy')->middleware('permissions:can_edit_holi_day_setting');
    Route::post('getholidays', 'API\HolidayController@sortedList')->middleware('permissions:can_edit_holi_day_setting');

    // View Route
    Route::get('/settings', 'API\SettingController@index');
    Route::get('/bookings', 'API\BookingController@bookingIndex')->middleware('permissions:can_manage_booking');
    Route::get('/services', 'API\ServiceController@services')->middleware('permissions:can_add_service');

    //client bookings
    Route::get('/bookingsclient', 'API\BookingController@bookingIndex');
    Route::post('/bookinglistclient', 'API\ClientDashboardController@getBookingData');

    // App Setting Route
    Route::post('/basic-setting', 'API\SettingController@basicSettingSave')->middleware('permissions:can_manage_application_setting');;
    Route::get('/basicsettingdata', 'API\SettingController@basicSettingData');
    Route::get('/timezone', 'API\SettingController@getTimezone');
    Route::get('/knowDefaultRowSettings', 'API\SettingController@knowDefaultRowSettings');

    Route::get('/dateformat', 'API\SettingController@dateFormat');
    // Invite Route
    Route::post('/invite', 'API\InviteController@process')->middleware('permissions:invite');
    Route::get('/allroleid', 'API\InviteController@getRoleId');

    // Booking Route
    Route::get('/allbooking', 'API\BookingController@allBookins');
    Route::get('/inovoice-pdf/{id}', 'API\BookingController@generateInvoice');
    Route::post('/bookingshow', 'API\BookingController@getBookingData');
    Route::post('/actionbooking/{id}', 'API\BookingController@action')->middleware('permissions:can_manage_booking');
    Route::get('/booking-payment/{id}', 'API\BookingController@bookingPaymentDetails')->middleware('permissions:can_manage_booking');

    // Role Route
    Route::post('/roletitle', 'API\RoleController@allData');
    Route::get('/roletitleuser', 'API\RoleController@allDataUser');
    Route::post('/addrole', 'API\RoleController@store')->middleware('permissions:roles');
    Route::post('/addrole/{id}', 'API\RoleController@update')->middleware('permissions:roles');
    Route::get('/rolepermissions/{id}', 'API\RoleController@getRolePermissions');
    Route::get('/rolepermissions/', 'API\RoleController@getRoleWithout');
    Route::post('/deleterole/{id}', 'API\RoleController@delete')->middleware('permissions:roles');

    // Notification Route
    Route::get('notify', 'API\NotificationController@index');
    Route::post('/upnotify/{id}', 'API\NotificationController@update');
    Route::post('countup/{id}', 'API\NotificationController@countUp');
    Route::get('notification', 'API\NotificationController@allNotification');

    //all users
    Route::post('/roleassign/{id}', 'API\RoleAssignController@update')->middleware('permissions:roles');
    Route::post('userlist', 'API\UserController@getUserList')->middleware('permissions:roles');
    Route::get('/user/{id}', 'API\UserController@userDetails');
    Route::post('/disableEnableUser/{id}', 'API\UserController@disableEnableUser')->middleware('permissions:roles');

    // Client setting
    Route::get('clientsetting', 'API\SettingController@clientSetting');
    Route::post('clientsetting', 'API\SettingController@updateClientSetting')->middleware('permissions:can_manage_client_setting');
    Route::get('/clients', 'API\ClientController@index')->middleware('permissions:can_manage_client_setting');
    Route::post('clientlist', 'API\ClientController@allClients')->middleware('permissions:can_manage_client_setting');
    Route::post('/clients/{id}', 'API\ClientController@updateClient')->middleware('permissions:can_manage_client_setting');
    Route::get('/client/{id}', 'API\ClientController@show')->middleware('permissions:can_manage_client_setting');
    Route::post('/client-bookings/{id}', 'API\ClientController@clientBookingList')->middleware('permissions:can_manage_client_setting');
    Route::get('/clients/{id}', 'API\ClientController@userDetails')->middleware('permissions:can_manage_client_setting');
    Route::get('/booking-details/{id}/{flag?}', 'API\BookingController@bookingDetails')->middleware('permissions:can_manage_booking');
    Route::get('/clientdashboarddata', 'API\ClientDashboardController@getData');
    Route::post('/bookinglist', 'API\ClientDashboardController@getBookingData')->middleware('permissions:can_manage_booking');

    Route::get('/invoice-details/{id}', 'API\BookingController@invoiceDetails');

    // Report route
    Route::get('/reports', 'API\ReportController@index')->middleware('permissions:can_see_reports');
    Route::get('/booking-report/{id}/{date}', 'API\ReportController@getReport')->middleware('permissions:can_see_reports');

    Route::get('/getlangdir', 'API\LocalizationController@getLangDir');

    // Payments Route
    Route::get('/payments', 'API\PaymentController@paymentIndex');
    Route::post('/payments', 'API\PaymentController@index');
    Route::post('/payment/store', 'API\PaymentController@store')->middleware('permissions:can_manage_payment_methods');
    Route::get('/payments/{id}', 'API\PaymentController@show')->middleware('permissions:can_manage_payment_methods');
    Route::post('/payments/{id}', 'API\PaymentController@updatePayment')->middleware('permissions:can_manage_payment_methods');
    Route::get('/payment/payment-details/{id}', 'API\PaymentController@duePayment');
    Route::post('/payment/payment-details', 'API\PaymentController@manualPayment')->middleware('permissions:can_manage_booking');
    Route::post('/payment-delete/{id}', 'API\PaymentController@destroy')->middleware('permissions:can_manage_payment_methods');
    Route::post('/manualpayment/{id}', 'API\PaymentController@manualPayment')->middleware('permissions:can_manage_booking');

    // Updates Route
    Route::get('/gain-update', 'API\UpdateController@applicationVersion');
    Route::get('/update-version-list', 'API\UpdateController@versionUpdateList');
    Route::post('/install-new-version/{version}', 'API\UpdateController@updateAction');
    Route::get('/update-list', 'API\UpdateController@updateAppUrl');
    Route::get('/curl_get_contents', 'API\UpdateController@curl_get_contents');
    Route::get('/nexInstallVersion', 'API\UpdateController@nexInstallVersion');


    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
    });

});

Route::post('/paypal-transaction-complete', 'API\PaymentController@paypalPayment');

//service slot timings
Route::get('/servicetiming/{id}/{date}', 'API\ServiceController@getTiming');
Route::get('/servicetiming/{id}/', 'API\ServiceController@getTimingZero');
Route::get('serviceshowforbooking', 'API\ServiceController@getServiceAndOffDays');

Route::get('/servicelist', 'API\ServiceController@activeData');

Route::get('/serviceid/{id}', 'API\ServiceController@show');

Route::get('/bookservice/{id}', 'API\BookingController@index');

Route::get('/getcurrency', 'API\SettingController@getCurrency');

Route::post('/bookservice/{id}', 'API\BookingController@setBooking');

Route::get('/paymentstripe', 'API\PaymentController@paymentForm');
Route::post('/paymentstripe','API\PaymentController@paymentFunction');

Route::get('/getpmethods','API\PaymentController@getAllMethods');

// Localization
Route::get('/js/lang.js', function () {
    $strings = Cache::rememberForever('lang.js', function () {
        $lang = config('app.locale');
        $files = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];
        foreach ($files as $file) {
            $name = basename($file, '.php');
            if ($name !== "custom" && $name !== "default") {
                $new_keys = require $file;
                $strings = array_merge($strings, $new_keys);
            }
        }
        return $strings;
    });
    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode(array("lang" => $strings)) . ';');
    exit();
})->name('assets.lang');