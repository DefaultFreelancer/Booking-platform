@extends('layouts.publicapp')

@section('title', trans('lang.register').' '.'-')

@section('content')

    <register-form emailadd="{{$email}}" token="{{$token}}"></register-form>

@endsection

