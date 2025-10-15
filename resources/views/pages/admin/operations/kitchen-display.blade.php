@extends('layouts.modules-layouts.operations-screens-layout')
@section('title') Kitchen Display System | Tinatangi Cafe @endsection
@section('topTitle') Tinatangi Cafe | Kitchen Display @endsection
@section('screen') KDS @endsection
@section('posTopNav') d-none @endsection
@section('content')

@endsection
@section('scripts')
    <script src="{{ asset('source/laravel_echo/echo.iife.js') }}"></script>
    <script src="{{ asset('source/laravel_echo/pusher.min.js') }}"></script>
    <script src="{{ asset('js/kitchenDisplay.js') }}"></script>
@endsection
