<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use http\Env\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use League\Flysystem\Config;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $userss;
    public $settingConfig;
    public $appUrl;
    public $publicPath;
    public $publicfilePath;
    public $currency_symbol;
    public $currency_format;
    public $thousand_separator;
    public $decimal_separator;
    public $number_of_decimal;
    public $currentUserid;
    public $app_logo;
    public $app_name;
    public $sender_mail;
    public $currency_code;
    public $time_format;

    public function __construct()
    {
        $installCheck = config('gain.installed');
        $this->appUrl = \Request::root();
        $this->publicPath = $this->appUrl;

        if ($installCheck === true) {
            $this->userss = Auth::user();

            if (Schema::hasTable('settings')) {

                $this->settingConfig = new Config();
                $data = Setting::all();

                foreach ($data as $datum) {

                    $this->settingConfig->set($datum->setting_name, $datum->setting_value);
                }
            }
        }

        $installCheck = config('gain.installed');

        if ($installCheck == true) {
            $this->currency_format = $this->settingConfig->get('currency_format');
            $this->currency_symbol = $this->settingConfig->get('currency_symbol');
            $this->thousand_separator = $this->settingConfig->get('thousand_separator');
            $this->decimal_separator = $this->settingConfig->get('decimal_separator');
            $this->number_of_decimal = $this->settingConfig->get('number_of_decimal');
            $this->app_logo = $this->settingConfig->get('app_logo');
            $this->app_name = $this->settingConfig->get('app_name');
            $this->sender_mail = $this->settingConfig->get('email_from_address');
            $this->currency_code = $this->settingConfig->get('currency_code');
            $this->time_format = $this->settingConfig->get('time_format');
        }
    }

    public function getDomainUrl()
    {
        $appurl = $this->appUrl;

        return $appurl;
    }


}
