@extends('layouts.app')
@section('title') Human Resources Dashboard @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('human_resources') d-block @endsection
@section('finance') d-none @endsection
@section('procurement') d-none @endsection
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
                                <div class="stats-icon blue">
                                    <i class="iconly-boldProfile"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Total Employees</h6>
                                <h6 class="font-extrabold mb-0">{{ $totalActive }}</h6>
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
                                <h6 class="font-extrabold mb-0">{{ $newHires }}</h6>
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
                            Minutes Rendered Today: <span id="totalHours" class="text-primary">0 minutes</span>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employees Attendance</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
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
    <style>
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

        @media (max-width: 576px) {
            #realtimeClock {
                font-size: 2rem;
                text-align: center;
            }

            .time-record {
                font-size: 16px;
                text-align: center;
            }

            .btn-lg {
                width: 100%;
                margin-bottom: 10px;
            }

            .card {
                padding: 15px;
                margin-bottom: 1rem;
            }

            .card-header {
                padding: 0.75rem 1rem;
                text-align: center;
            }
        }
    </style>
@endsection
@section('scripts')
    <script src="{{ asset('js/employeeAttendance.js') }}"></script>
@endsection