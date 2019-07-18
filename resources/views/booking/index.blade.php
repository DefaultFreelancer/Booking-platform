@extends( Auth::user()->is_admin != 1 && Auth::user()->role_id == 0  ?  'layouts.clientapp' : 'layouts.app' )

@inject('perm', 'App\Http\Controllers\API\PermissionController')

@section('title', trans('lang.booking').' '.'-')

@section('content')

    @if(Auth::user()->is_admin != 1 && Auth::user()->role_id == 0)
        <client-booking-list></client-booking-list>
    @else
        <booking-index bookper={{$perm->bookingAddPermission()}} clientsetting={{$perm->clientSettingsPermission()}}></booking-index>
    @endif

@endsection