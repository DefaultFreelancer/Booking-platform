@include('layouts.include.head')
<body>
@inject('appConfig', 'App\Http\Controllers\Controller')
@inject('basicData', 'App\Http\Controllers\API\SettingController')
    <div id="app">
        <main id="app">
            @yield('content')
        </main>
    </div>
    <script>
    window.appConfig ={
        appUrl: "<?php echo $appConfig->appUrl ?>",
        publicPath: "<?php echo $appConfig->publicPath ?>",
        currencySymbol: "<?php echo $appConfig->currency_symbol?>",
        currencyCode: "<?php echo $appConfig->currency_code?>",
        currencyFormat: "<?php echo $appConfig->currency_format?>",
        thousandSeparator: "<?php echo $appConfig->thousand_separator?>",
        decimalSeparator: "<?php echo $appConfig->decimal_separator?>",
        numberOfDecimal: "<?php echo $appConfig->number_of_decimal?>",
        appLogo: "<?php echo $appConfig->app_logo?>",
        appName: "<?php echo $appConfig->app_name?>",
    }
    </script>

@include('layouts.include.footer')