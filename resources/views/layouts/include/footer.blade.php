@inject('app', 'App\Http\Controllers\API\SettingController')
<!-- Scripts -->
<?php
    $publicPath = $app->getAppPublicPath();
    $paypalId = $app->getPaypalClientId();
    $currency_code = $app->getCurrencyCode();
    $appDetails = config('gain');
    $scriptSources=[
        [
            'comment' =>'<!--Import lang.js-->',
            'src' => asset('/js/lang.js'),
        ],
        [
            'comment' =>'<!--Import app.js-->',
            'src' => asset($publicPath.'/js/app.js'),
        ],
        [
            'comment' =>'<!--Import accounting.js-->',
            'src' => asset($publicPath.'/js/accounting.js'),
        ],
        [
            'comment' =>'<!--Import summernote-lite.js-->',
            'src' => asset($publicPath.'/summernote-0.8.9/summernote-lite.js'),
        ],

    ];
    foreach ($scriptSources as $scriptSource)
    {
        if ($scriptSource['comment'])
        {
            echo $scriptSource['comment']."\n";
        }

        echo "<script src='" .$scriptSource['src']. "?app_version=".$appDetails['app_version']. "'></script> \n";

    }
?>



<script src="https://checkout.stripe.com/checkout.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypalId ?>&disable-funding=credit,card&currency=<?php echo $currency_code ?>"></script>

</body>
</html>
