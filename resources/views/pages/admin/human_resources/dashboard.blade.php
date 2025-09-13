@extends('layouts.hr-app')
@section('title') Human Resources Dashboard @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('dsh') active @endsection
@section('emplMngt')@endsection
@section('appMngt')@endsection
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
    <section class="section">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="iconly-boldShow"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Profile Views</h6>
                                <h6 class="font-extrabold mb-0">112.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="iconly-boldProfile"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Total Employees</h6>
                                <h6 class="font-extrabold mb-0">183.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="iconly-boldAdd-User"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">New Hires</h6>
                                <h6 class="font-extrabold mb-0">80.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon red">
                                    <i class="iconly-boldBookmark"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Saved Post</h6>
                                <h6 class="font-extrabold mb-0">112</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="card-body text-center">
                    <h1 id="realtimeClock" class="text-muted mb-3"></h1>

                    <form id="attendanceForm">
                        @csrf
                        <input type="hidden" name="employee_id" id="employeeIdInput" value="{{ Auth::user()->id ?? '' }}">

                        <div class="d-flex justify-content-between mb-3">
                            <button type="button" class="btn btn-success btn-lg" id="timeInBtn">Time In</button>
                            <button type="button" class="btn btn-danger btn-lg" id="timeOutBtn">Time Out</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 px-3">
                            <p class="time-record">
                                Time In: <span id="timeInDisplay" class="text-success">--:--:--</span>
                            </p>
                            <p class="time-record">
                                Time Out: <span id="timeOutDisplay" class="text-danger">--:--:--</span>
                            </p>
                        </div>

                        <p class="time-record mt-2">
                            Total Minutes Rendered: <span id="totalHours" class="text-primary">0 minutes</span>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employee Attendance</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-bordered table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Total Minutes</th>
                                <th>Tardiness Minutes</th>
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
    <style>
        .action-btns .btn {
            width: auto;
            margin: 2px;
        }

        .table-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .table-active {
            background-color: rgba(13, 110, 253, 0.1);
        }

        #attendanceTable {
            font-size: 0.9rem;
        }

        #realtimeClock {
            font-size: 3rem;
            font-weight: bold;
            color:
                #007bff;
        }

        .time-record {
            font-size: 18px;
            font-weight: bold;
        }

        .btn-lg {
            width: 48%;
        }

        .card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1rem 1.25rem;
        }

        .table-responsive {
            margin-top: 0.5rem;
        }
    </style>
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}   "></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
    <script src="{{ asset('js/employeeAttendance.js') }}"></script>
@endsection