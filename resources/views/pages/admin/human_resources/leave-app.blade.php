@extends('layouts.app')
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
@section('sbi3')
@endsection
@section('sbi4')active
@endsection
@section('headings') Leave Approval @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.employees') }}">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Leaves</li>
        </ol>
    </nav>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Default Layout</h4>
            </div>
            <div class="card-body">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Magnam, commodi? Ullam quaerat
                similique iusto
                temporibus, vero aliquam praesentium, odit deserunt eaque nihil saepe hic deleniti? Placeat
                delectus
                quibusdam ratione ullam!
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>

    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
@endsection