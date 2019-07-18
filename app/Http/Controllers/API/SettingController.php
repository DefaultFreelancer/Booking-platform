<?php

namespace App\Http\Controllers\API;

use App\Libraries\AllSettingsFormat;
use App\Libraries\Email;
use App\Models\Setting;
use App\Models\ClientSetting;
use App\Models\PaymentMethod;
use Cache;
use DateTimeZone;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use App\Libraries\Permissions;
use Config;
use Illuminate\Support\Facades\Lang;


class SettingController extends Controller
{

    public function permissionCheck()
    {
        $controller = new Permissions;

        return $controller;
    }

    public function index()
    {
        return view('setting.index');
    }

    public function getAppPublicPath()
    {
        $publicPath = $this->publicPath;

        return $publicPath;
    }

    public function cacheData()
    {
        $data = Setting::saveCacheData();

        return $data;

    }

    public function getTimezone()
    {
        return $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

    public function offdays(Request $request)
    {
        $offday_setting = $request->offday_setting;
        $offday = Setting::where('setting_name', '=', 'offday_setting')->first();

        if ($offday == null) {
            Setting::create(['setting_name' => 'offday_setting', 'setting_value' => '', 'setting_type' => 'app', 'created_by' => Auth::user()->id]);
        }

        Setting::where('setting_name', 'offday_setting')->update(['setting_value' => implode(',', $offday_setting)]);

        Cache::flush() && $this->cacheData();
    }

    public function offdaysData()
    {
        return $this->cacheData();
    }

    public function dateFormat()
    {
        $settingAll = new AllSettingsFormat;

        return $settingAll->getDateFormat();
    }

    public function emailSettingSave(Request $request)
    {
        $this->validate($request, [
            'email_from_name' => 'required',
            'email_from_address' => 'required',
            'email_driver' => 'required',
        ]);

        $name = $request->email_from_name;
        $address = $request->email_from_address;
        $driver = $request->email_driver;
        $host = $request->email_smtp_host;
        $port = $request->email_port;
        $pass = $request->email_smtp_password;
        $type = $request->email_encryption_type;
        $mandrill_api = $request->mandrill_api;
        $sparkpost_api = $request->sparkpost_api;
        $mailgun_domain = $request->mailgun_domain;
        $mailgun_api = $request->mailgun_api;

        //NB: do not remove below commented codes (For future use)
        /*
            $ses_key=$request->ses_key;
            $ses_secret=$request->ses_secret;
            $ses_region=$request->ses_region;
        */

        Setting::where('setting_name', 'email_from_name')->update(['setting_value' => $name]);
        Setting::where('setting_name', 'email_from_address')->update(['setting_value' => $address]);
        Setting::where('setting_name', 'email_driver')->update(['setting_value' => $driver]);
        Setting::where('setting_name', 'email_smtp_host')->update(['setting_value' => $host]);
        Setting::where('setting_name', 'email_port')->update(['setting_value' => $port]);
        Setting::where('setting_name', 'email_smtp_password')->update(['setting_value' => $pass]);
        Setting::where('setting_name', 'email_encryption_type')->update(['setting_value' => $type]);
        Setting::where('setting_name', 'mandrill_api')->update(['setting_value' => $mandrill_api]);
        Setting::where('setting_name', 'sparkpost_api')->update(['setting_value' => $sparkpost_api]);
        Setting::where('setting_name', 'mailgun_domain')->update(['setting_value' => $mailgun_domain]);
        Setting::where('setting_name', 'mailgun_api')->update(['setting_value' => $mailgun_api]);

        //NB: do not remove below commented codes (For future use)
        /*
           Setting::where('setting_name', 'ses_key')->update(['setting_value' => $ses_key]);
           Setting::where('setting_name', 'ses_secret')->update(['setting_value' => $ses_secret]);
           Setting::where('setting_name', 'ses_region')->update(['setting_value' => $ses_region]);
        */

        Cache::flush() && $this->cacheData();

        if ($request->test_mail != '') {

            return $this->testMail($request->test_mail);
        }



    }

    public function emailSettingData()
    {
        return $this->cacheData();
    }

    public function basicSettingSave(Request $request)
    {
        $time_format = $request->time_format;
        $date_format = $request->date_format;
        $time_zone = $request->time_zone;
        $currency_symbol = $request->currency_symbol;
        $currency_format = $request->currency_format;
        $currency_code = trim($request->currency_code);
        $thousand_separator = $request->thousand_separator;
        $language_setting = $request->language_setting;
        $decimal_separator = $request->decimal_separator;
        $number_of_decimal = $request->number_of_decimal;
        $max_row_per_table = $request->max_row_per_table;
        $app_name = $request->app_name;
        $landing_page_message = $request->landing_page_message;
        $landing_page_header = $request->landing_page_header;
        $company_name = $request->company_name;
        $company_info = $request->company_info;

        $app_logo = '';

        Setting::where('setting_name', 'time_format')->update(['setting_value' => $time_format]);
        Setting::where('setting_name', 'date_format')->update(['setting_value' => $date_format]);
        Setting::where('setting_name', 'time_zone')->update(['setting_value' => $time_zone]);
        Setting::where('setting_name', 'currency_symbol')->update(['setting_value' => $currency_symbol]);
        Setting::where('setting_name', 'currency_format')->update(['setting_value' => $currency_format]);
        Setting::where('setting_name', 'currency_code')->update(['setting_value' => $currency_code]);
        Setting::where('setting_name', 'thousand_separator')->update(['setting_value' => $thousand_separator]);
        Setting::where('setting_name', 'language_setting')->update(['setting_value' => $language_setting]);
        Setting::where('setting_name', 'decimal_separator')->update(['setting_value' => $decimal_separator]);
        Setting::where('setting_name', 'number_of_decimal')->update(['setting_value' => $number_of_decimal]);
        Setting::where('setting_name', 'max_row_per_table')->update(['setting_value' => $max_row_per_table]);
        Setting::where('setting_name', 'app_name')->update(['setting_value' => $app_name]);
        Setting::where('setting_name', 'landing_page_message')->update(['setting_value' => $landing_page_message]);
        Setting::where('setting_name', 'landing_page_header')->update(['setting_value' => $landing_page_header]);
        Setting::where('setting_name', 'company_name')->update(['setting_value' => $company_name]);
        Setting::where('setting_name', 'company_info')->update(['setting_value' => $company_info]);

        if ($request->invoice_logo) {

            if ($request->invoice_logo == $this->settingConfig->get('invoice_logo')) {
                $invoice_logo = $this->settingConfig->get('invoice_logo');

            } else {

                if ($file = $request->invoice_logo) {
                    list($type, $file) = explode(';', $request->invoice_logo);
                    list(, $extension) = explode('/', $type);
                    //list(, $file) = explode(',', $file);
                    $fileName = uniqid() . '.' . $extension;
                    $source = fopen($request->invoice_logo, 'r');
                    $destination = fopen(public_path() . '/uploads/invoice/' . $fileName, 'w');
                    stream_copy_to_stream($source, $destination);
                    fclose($source);
                    fclose($destination);

                    $invoice_logo = $fileName;
                }

                if ($this->settingConfig->get('invoice_logo') != 'invoice.jpg' && $this->settingConfig->get('invoice_logo') != '' && file_exists(public_path() . '/uploads/invoice/' . $this->settingConfig->get('invoice_logo'))) {
                    unlink(public_path() . '/uploads/invoice/' . $this->settingConfig->get('invoice_logo'));
                }
            }

            Setting::where('setting_name', 'invoice_logo')->update(['setting_value' => $invoice_logo]);
        }

        if ($request->app_logo) {

            if ($request->app_logo == $this->settingConfig->get('app_logo')) {
                $app_logo = $this->settingConfig->get('app_logo');

            } else {

                if ($file = $request->app_logo) {
                    list($type, $file) = explode(';', $request->app_logo);
                    list(, $extension) = explode('/', $type);
                    //list(, $file) = explode(',', $file);
                    $fileName = uniqid() . '.' . $extension;
                    $source = fopen($request->app_logo, 'r');
                    $destination = fopen(public_path() . '/uploads/logo/' . $fileName, 'w');
                    stream_copy_to_stream($source, $destination);
                    fclose($source);
                    fclose($destination);

                    $app_logo = $fileName;
                }

                if ($this->settingConfig->get('app_logo') != 'default-logo.png' && $this->settingConfig->get('app_logo') != '' && file_exists(public_path() . '/uploads/logo/' . $this->settingConfig->get('app_logo'))) {
                    unlink(public_path() . '/uploads/logo/' . $this->settingConfig->get('app_logo'));
                }
            }

            Setting::where('setting_name', 'app_logo')->update(['setting_value' => $app_logo]);
        }

        if ($request->background_image) {
            $File = new Filesystem;
            $path = public_path() . '/images/background/background-image.jpeg';

            if ($File->exists($path)) {
                unlink($path);
            }

            if ($file = $request->app_logo) {
                list($type, $file) = explode(';', $request->background_image);
                list(, $extension) = explode('/', $type);
                $fileName = 'background-image.jpeg';
                $source = fopen($request->background_image, 'r');
                $destination = fopen(public_path() . '/images/background/' . $fileName, 'w');
                stream_copy_to_stream($source, $destination);
                fclose($source);
                fclose($destination);
            }
        }

        session()->put('language_setting', $language_setting);
        //language cache clear
        Artisan::call('cache:clear');

        Cache::flush() && $this->cacheData();
    }

    public function getAppLogo()
    {
        $applogo = $this->settingConfig->get('app_logo');

        return $applogo;
    }

    public function basicSettingData()
    {
        return $this->cacheData();
    }

    public function testMail($email)
    {
        $appName = Setting::select('setting_value')->where('setting_name', 'email_from_name')->first()->setting_value;
        $sub = Lang::get('lang.test_mail');
        $emailHeader = '<html>
                           <div style="width: 35%; color: #333333; font-family: Helvetica; margin:auto; font-size: 125%; padding-bottom: 10px;">
                               <div style="text-align:center; padding-top: 10px; padding-bottom: 10px;">
                                   <h1>' . $appName . '</h1>
                               </div>
                               <div style="padding: 35px;padding-left:20px; border-bottom: 1px solid #cccccc; border-top: 1px solid #cccccc;">';
        $emailFooter = '        </div>
                           </div>
                       </html>';

        $text = $emailHeader . Lang::get('lang.this_is_a_test_email') . $emailFooter;

        $eSend = new Email;

        if ($eSend->sendEmail($text, $email, $sub)) {

            return response()->json(['message' => 'Success']);
        }

    }

    public function clientSetting()
    {
        $clientSetting = [
            'can_signup' => (int)Setting::select('setting_value')->where('setting_name', 'can_signup')->first()->setting_value,
            'can_login' => (int)Setting::select('setting_value')->where('setting_name', 'can_login')->first()->setting_value,
            'submit_booking_after_login' => (int)Setting::select('setting_value')->where('setting_name', 'submit_booking_after_login')->first()->setting_value,
        ];

        return $clientSetting;
    }

    public function updateClientSetting(Request $request)
    {
        if (count($request->all())) {
            Setting::where('setting_name', 'can_signup')->update(['setting_value' => $request->can_signup]);
            Setting::where('setting_name', 'can_login')->update(['setting_value' => $request->can_login]);
            Setting::where('setting_name', 'submit_booking_after_login')->update(['setting_value' => $request->submit_booking_after_login]);

            $response = [
                'msg' => Lang::get('lang.created_successfully'),
            ];

            return response()->json($response, 201);
        }
    }

    public function knowDefaultRowSettings()
    {
        $data = Setting::select('setting_value')->where('setting_name', 'max_row_per_table')->first()->setting_value;

        return $data;
    }

    public function dateTimeFormat()
    {
        $settingAll = new AllSettingsFormat;

        return $dateTimeFormat = strtoupper($settingAll->getDateFormat()) . " " . $settingAll->getTimeFormat();
    }

    public function currentUserId()
    {
        return Auth::user()->id;
    }

    public function getPaypalClientId()
    {
        $data = PaymentMethod::where('type', 'paypal')->first();
        $data = unserialize($data->option);
        return $data[1];
    }
    public function getCurrencyCode()
    {
       return  Setting::where('setting_name', 'currency_code')->first()->setting_value;
    }
}
