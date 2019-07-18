<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\PayPalClient;

class PaypalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('App\Libraries\PayPalClient', function ($app) {
            return new PayPalClient();
        });
    }
}
