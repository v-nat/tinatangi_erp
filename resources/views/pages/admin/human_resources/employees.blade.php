@extends('layouts.hr-app')
@section('title') Human Resources @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('dsh')
@endsection
@section('emplMngt')active
@endsection
@section('emplMngt2')active
@endsection
@section('sbi1')active
@endsection
@section('sbi2')
@endsection
@section('sbi3')
@endsection
@section('sbi4')
@endsection
@section('content')
    {{-- <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.employees') }}">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employees</li>
        </ol>
    </nav> --}}
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Employee List</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Employee Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Employees</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">

            <div class="card-header">
                Employee Table
            </div>
            <div class="card-body">
                <table class="table table-striped" id="employee_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Direct Supervisor</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>

    </section>
@endsection
@section('scripts')
    <script src="{{ asset('source/jquery/datatables.js') }}"></script>
    <script src="{{ asset('source/jquery/datatables.min.js') }}"></script>
    {{-- <link href="{{ asset( 'source/css/datatables.css') }}" rel="stylesheet"></link>
    <link href="{{ asset( 'source/css/datatables.min.css') }}" rel="stylesheet"></link> --}}
    
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}   "></script>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    {{--
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script> --}}
    <script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{ asset('assets/js/main2.js') }}"></script>
    <script src="{{ asset('js/hrEmployees.js') }}"></script>
@endsection