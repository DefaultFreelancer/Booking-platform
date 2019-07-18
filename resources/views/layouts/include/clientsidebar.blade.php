@inject('app', 'App\Http\Controllers\API\SettingController')
<?php
    $data = "$_SERVER[REQUEST_URI]";

?>
<client-sidebar route={{ $data }}
        applogo={{ $app->getAppLogo() }}>
</client-sidebar>
