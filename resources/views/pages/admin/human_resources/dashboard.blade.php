@extends('layouts.app')
@include('partials.human-resources-heading')
@section('dsh') active @endsection
@section('headings') Human Resources Dashboard @endsection

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">
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
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Pending Overtimes</h6>
                                <h6 class="font-extrabold mb-0">{{ $overtimes }}</h6>
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
                                    <i class="fa-solid fa-user-xmark"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Pending Leaves</h6>
                                <h6 class="font-extrabold mb-0">{{ $leaves }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Weekly Attendance Overview</h4>
                        <small class="text-muted">Current week breakdown by status</small>
                    </div>
                    <div class="card-body">
                        <div id="chart-weekly-attendance"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Headcount by Department</h4>
                        <small class="text-muted">Active employees</small>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div id="chart-department-headcount" style="width:100%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Overtime Requests (Last 6 Months)</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-overtime-trend"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Total Net Payroll (Last 6 Months)</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-payroll-trend"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Employees Today's Attendance</h4>
                <button class="btn btn-outline-primary btn-sm" id="btn-refresh-dashboard-attendance">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-sm table-hover dataTable no-footer"
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
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employees On Leave</h4>
            </div>
            <div class="card-body">
                <div id='calendar' data-events-url="{{ route('hr.schedules') }}"></div>
            </div>
        </div>
        </div>


    </section>


@endsection
@section('scripts')
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    @vite('resources/js/hrDashboard.js')
@endsection
