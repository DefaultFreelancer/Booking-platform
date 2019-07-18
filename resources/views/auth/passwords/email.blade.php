@extends('layouts.publicapp')

@section('title', trans('lang.forgot_password').' '.'-')

@section('content')

    <div id="app">
        <body>
        <div class=" back-img row margin-fix">
            <div class="col s12 m6 offset-m6 l4 offset-l8 no-padding">
                <div class="content-parent">
                    @if (session('status'))
                        <div class="card-content green-text">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="content-child">
                        <email-reset></email-reset>
                    </div>
                </div>
            </div>
        </div>
        </body>
    </div>

@endsection