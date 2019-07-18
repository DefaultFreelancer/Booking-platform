<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Config;

class Language
{
    public function handle($request, Closure $next)
    {

        if (Session::has('language_setting'))
        {
            $locale = Session::get('language_setting', Config::get('app.locale'));
        }
        else
        {
            $locale = 'english';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
