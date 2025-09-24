@extends('layouts.app')
@section('title') Finance Risk Management @endsection
@section('sidebar-title') Finance Risk Management @endsection
@section('human_resources') d-none @endsection
@section('finance') d-block @endsection
@section('procurement') d-none @endsection
@section('financePurchases') active
@endsection
@section('headings') Requests List @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance.payroll') }}">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchases</li>
        </ol>
    </nav>
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}   "></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
@endsection