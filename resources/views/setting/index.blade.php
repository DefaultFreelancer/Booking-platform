@extends('layouts.app')

@section('title', trans("lang.settings").' '.'-')

@section('content')
    @inject('perm', 'App\Http\Controllers\API\PermissionController')

    <setting-index
            roles={{$perm->roleManagePermission()}}
            users={{$perm->userManagePermission()}}
            app_settings={{$perm->appsManagePermission()}}
            email_settings={{$perm->emailsManagePermission()}}
            off_day={{$perm->offdaysManagePermission()}}
            holiday={{$perm->holidaysManagePermission()}}
            email_templates={{$perm->etemplateManagePermission()}}
            client_settings={{$perm->clientSettingsPermission()}}
            payment_settings={{$perm->paymentSettingsPermission()}}
            updates={{$perm->updatePermission()}}

    ></setting-index>

@endsection
