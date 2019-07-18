@extends('layouts.publicapp')

@section('title', trans('lang.reset_password').' '.'-')

@section('content')

    <reset-password token="{{ $token }}" emailreset="{{ $emailreset }}" ></reset-password>

@endsection