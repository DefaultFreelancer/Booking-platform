@extends( Auth::user()->is_admin != 1 && Auth::user()->role_id == 0  ?  'layouts.clientapp' : 'layouts.app' )

@section('title', trans("lang.account_update"))

@section('content')

    <change-password :user="{{ $user }}"></change-password>

@endsection
