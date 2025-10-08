@extends('layouts.app')
@include('partials.general-employee-heading')
@section('headings') Attendances @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" onclick="history.back()">Employee</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attendances</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                My Attendance Records
            </div>
            <a class="d-none" id="employee_id" href="">{{$id}}</a>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="employeeAttendanceTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Total Minutes</th>
                                <th>Overtime</th>
                                <th>Tardiness</th>
                                <th>Leave Status</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/employeeAttendance.js') }}"></script>
@endsection
