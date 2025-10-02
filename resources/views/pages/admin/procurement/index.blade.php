@extends('layouts.app')
@section('title') Procurement Management @endsection
@section('sidebar-title') Procurement Management @endsection
@section('human_resources') d-none @endsection
@section('finance') d-none @endsection
@section('procurement') d-block @endsection
@section('procurementIndex') active @endsection
@section('headings') Index @endsection
@section('content')


@endsection
@section('scripts')
    <script src="{{ asset('assets/js/main2.js') }}   "></script>
@endsection