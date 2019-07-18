@extends( Auth::user()->is_admin != 1 && Auth::user()->role_id == 0  ?  'layouts.clientapp' : 'layouts.app' )

@section('title', trans('lang.notification').' '.'-')

@section('content')
    <all-notification-view
            :data_notify="{{$data_notify}}"
           :auth_profile="{{Auth::User()}}">
    </all-notification-view>
@endsection