@extends( Auth::user()->is_admin != 1 && Auth::user()->role_id == 0  ?  'layouts.clientapp' : 'layouts.app' )

@section('title', trans("lang.profile_title").' '.'-')

@section('content')

    <profile :profile="{{ $profile }}"></profile>

@endsection
