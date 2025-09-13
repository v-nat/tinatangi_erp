@extends('layouts.hr-app')
@section('title') Human Resources @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('dsh') 
@endsection
@section('emplMngt')@endsection
@section('emplMngt2')@endsection
@section('appMngt')active
@endsection
@section('appMngt2')active
@endsection
@section('sbi1')
@endsection
@section('sbi2')
@endsection
@section('sbi3')active
@endsection
@section('sbi4')
@endsection
@section('headings') Overtime Approval @endsection

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('hr.employees') }}">Employee Management</a></li>
        <li class="breadcrumb-item active" aria-current="page">Overtimes</li>
    </ol>
</nav>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>

    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
@endsection