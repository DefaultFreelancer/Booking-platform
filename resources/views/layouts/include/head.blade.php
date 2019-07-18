@inject('app', 'App\Http\Controllers\API\SettingController')
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <?php
        if (DB::table('settings')->where('setting_name', '=', 'app_name')->exists())
            {
            $app_name = DB::table('settings')->where('setting_name','app_name')->select('setting_value')->first()->setting_value;
            }
        else
            {
                $app_name = "";
            }

    ?>
    @if(!$app_name)
        <title>@yield('title')</title>
    @else
        <title>@yield('title') {{$app_name}}</title>
    @endif

    <!-- Styles -->
    <?php
    $publicPath = $app->getAppPublicPath();
    $appDetails = config('gain');
    $cssLinks=[
        [
            'comment' =>'<!--Import favicon-->',
            'assets' => asset($publicPath.'/images/favicon/favicon.png'),
            'rel' => 'shortcut icon',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import Open Sans Font-->',
            'assets'=> asset($publicPath.'/materialize-sass/fonts/Open-Sans/open-sans.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import Raleway Font-->',
            'assets'=> asset($publicPath.'/materialize-sass/fonts/Raleway/raleway.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import Material-icon-->',
            'assets' => asset($publicPath.'/materialize-sass/icon/icon.css'),
            'rel' => 'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import line-awesome-icon-->',
            'assets'=> asset($publicPath.'/line-awesome/css/line-awesome.min.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' => '',
            'assets'=> asset($publicPath.'/line-awesome/css/line-awesome-font-awesome.min.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import materialize.css-->',
            'assets'=> asset($publicPath.'/materialize-sass/css/materialize.css'),
            'rel'=>'stylesheet',
            'media' => "screen,projection"
        ],
        [
            'comment' =>'<!--Import style.css-->',
            'assets'=> asset($publicPath.'/css/style.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import profile-style.css-->',
            'assets'=> asset($publicPath.'/css/profile-style.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import navbar-sidebar-style.css-->',
            'assets'=> asset($publicPath.'/css/navbar-sidebar-style.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import dashboard-style.css-->',
            'assets'=> asset($publicPath.'/css/dashboard-style.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import calendar-style.css-->',
            'assets'=> asset($publicPath.'/css/calendar-style.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import calendar-style.css-->',
            'assets'=> asset($publicPath.'/css/calendar-seven-days-style.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import main-layout.css-->',
            'assets'=> asset($publicPath.'/css/main-layout.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--Import jquery.timepicker.css-->',
            'assets'=>  asset($publicPath.'/css/jquery.timepicker.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--summernote-lite.css-->',
            'assets'=>  asset($publicPath.'/summernote-0.8.9/summernote-lite.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],
        [
            'comment' =>'<!--animate.css-->',
            'assets'=>  asset($publicPath.'/css/animate.css'),
            'rel'=>'stylesheet',
            'media' => '',
        ],

    ];
    foreach ($cssLinks as $cssLink)
        {
           if ($cssLink['comment'])
           {
               echo $cssLink['comment']."\n";
           }

           if($cssLink['media'])
           {
               echo "<link href='" .$cssLink['assets'].'?app_version='.$appDetails['app_version']. "' rel='".$cssLink['rel']."' media='".$cssLink['media']."'>\n";
           }
           else
           {
               echo "<link href='" .$cssLink['assets'].'?app_version='.$appDetails['app_version']. "' rel='".$cssLink['rel']."'>\n";
           }

        }
    ?>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
</head>
