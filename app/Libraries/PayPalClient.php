<?php

namespace App\Libraries;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use App\Models\PaymentMethod;

ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');


class PayPalClient
{
    public static function client()
    {
        return new PayPalHttpClient(self::environment());
    }

    /**
     * Setting up and Returns PayPal SDK environment with PayPal Access credentials.
     * For demo purpose, we are using SandboxEnvironment. In production this will be
     * LiveEnvironment.
     */
    public static function environment()
    {
        $paypalDetails = PaymentMethod::where('type','paypal')->first();
        $options = unserialize($paypalDetails->option);
        $clientSecret = $options[0];
        $clientId =$options[1];
        $mode = $options[2];

       if($mode == 1){

           return new ProductionEnvironment($clientId, $clientSecret);

       }else{

           return new SandboxEnvironment($clientId, $clientSecret);
       }

    }
}