@inject('perm', 'App\Http\Controllers\API\PermissionController')
@inject('app', 'App\Http\Controllers\API\SettingController')

<nav-bar :profile="{{ Auth::user() }}"
         services={{$perm->serviceManagePermission()}}
         booking={{$perm->bookingManagePermission()}}
         userroles={{$perm->roleManagePermission()}}
         usermanage={{$perm->userManagePermission()}}
         appsettings={{$perm->appsManagePermission()}}
         emailsettings={{$perm->emailsManagePermission()}}
         offdaysettings={{$perm->offdaysManagePermission()}}
         holidaysettings={{$perm->holidaysManagePermission()}}
         emailtemplate={{$perm->etemplateManagePermission()}}
         clientmanage={{$perm->clientManagePermission()}}
         clientsetting={{$perm->clientSettingsPermission()}}
         reportpermission={{$perm->reportPermission()}}
    >
</nav-bar>