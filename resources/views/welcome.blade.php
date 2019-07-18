@extends('layouts.publicapp')

@section('title')

@section('content')

    <?php $sessionStatus = ''; ?>
    @if(session('status'))
        <?php $sessionStatus = session('status'); ?>
    @endif

    @guest
        <calendar-seven-days sessionstatus="{{ $sessionStatus }}" landing_page_message="{{ $landingPageMessage }}" landing_page_header="{{ $landingPageHeader }}" :checksignup="{{ $signupCheck }}" :service ="{{$service}}" :user="null"></calendar-seven-days>
    @else
        <calendar-seven-days sessionstatus="{{ $sessionStatus }}" landing_page_message="{{ $landingPageMessage }}" landing_page_header="{{ $landingPageHeader }}" :checksignup="{{ $signupCheck }}" :service ="{{$service}}" :user="{{ $user }}" ></calendar-seven-days>
    @endguest

@endsection
