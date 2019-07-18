@inject('app', 'App\Http\Controllers\API\SettingController')

<client-navbar :profile="{{ Auth::user() }}"
               applogo={{ $app->getAppLogo() }}>
</client-navbar>