@include('layouts.include.head')
<body>
@inject('appConfig', 'App\Http\Controllers\Controller')
@inject('basicData', 'App\Http\Controllers\API\SettingController')
    <div id="app">


            @include('layouts.include.clientnavbar')
            @include('layouts.include.clientsidebar')


        <main id="app">

            @yield('content')

        </main>
    </div>
<script>
    window.appConfig ={
        appUrl: "<?php echo $appConfig->appUrl ?>",
        publicPath: "<?php echo $appConfig->publicPath ?>",
        dateTimeFormat: "<?php echo $basicData->dateTimeFormat() ?>",
        dateFormat: "<?php echo $basicData->dateFormat() ?>",
        knowDefaultRowSettings: "<?php echo $basicData->knowDefaultRowSettings() ?>",
        currentUserId: "<?php echo $basicData->currentUserId() ?>",
        currencySymbol: "<?php echo $appConfig->currency_symbol?>",
        currencyFormat: "<?php echo $appConfig->currency_format?>",
        thousandSeparator: "<?php echo $appConfig->thousand_separator?>",
        decimalSeparator: "<?php echo $appConfig->decimal_separator?>",
        numberOfDecimal: "<?php echo $appConfig->number_of_decimal?>",
        appLogo: "<?php echo $appConfig->app_logo?>",
        appName: "<?php echo $appConfig->app_name?>",
        currencyCode: "<?php echo $appConfig->currency_code?>",
        timeFormat: "<?php echo $appConfig->time_format?>",
    }
</script>

@include('layouts.include.footer')