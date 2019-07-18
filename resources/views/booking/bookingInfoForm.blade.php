@extends('layouts.publicapp')

@section('title', 'Booking Form')

@section('content')
    <bookservie-form :id="{{$id}}"></bookservie-form>
@endsection
