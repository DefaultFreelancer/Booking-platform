@extends( Auth::user()->is_admin != 1 && Auth::user()->role_id == 0  ?  'layouts.clientapp' : 'layouts.app' )

@section('title', trans('lang.view_booking').' '.'-' )

@section('content')

    <booking-details :booking="{{ $booking }}"></booking-details>

@endsection