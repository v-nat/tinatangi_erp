@extends('layouts.hr-app')
@section('title') Human Resources Dashboard @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('dsh') active @endsection
@section('emplMngt')@endsection
@section('sbi1') @endsection
@section('sbi2') @endsection
@section('sbi3') @endsection
@section('sbi4') @endsection
@section('headings') Human Resources Dashboard @endsection

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">Human Resources</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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