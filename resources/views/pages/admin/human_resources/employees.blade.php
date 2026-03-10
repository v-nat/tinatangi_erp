@extends('layouts.app')
@include('partials.human-resources-heading')
@section('emplMngt')active @endsection
@section('emplMngt2')active @endsection
@section('sbi1')active @endsection
@section('headings') Employee List @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employees</li>
        </ol>
    </nav>

    <section class="section row">
        <div class="card col-12">
            <div class="card-header row gy-2 px-3 py-3 align-items-center">
                <div class="col-12 col-lg-6 d-flex flex-wrap align-items-center">
                    <h4 class="card-title mb-0 me-3">Employee List</h4>
                    <label for="department_filter" class="mb-0 me-2">Filter by Department:</label>
                    <select id="department_filter" class="form-select form-select-sm w-auto">
                        <option value="">All Departments</option>

                    </select>
                </div>

                <div class="col-12 col-lg-6 d-flex justify-content-lg-end align-items-center gap-2">
                    <div class="d-flex align-items-center" style="gap: 0.5rem;">
                        <div class="d-flex align-items-center">
                            <label for="payroll_start_date" class="payroll-date-label">Start:</label>
                            <input type="date" id="payroll_start_date" class="form-control form-control-sm"
                                style="width: 150px;">
                        </div>
                        <div class="d-flex align-items-center">
                            <label for="payroll_end_date" class="payroll-date-label">End:</label>
                            <input type="date" id="payroll_end_date" class="form-control form-control-sm"
                                style="width: 150px;">
                        </div>
                    </div>

                    <button id="batch_payroll_btn" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-money-check-dollar me-1"></i>
                        Generate Batch Payroll
                    </button>
                    <button id="btn-refresh-employee-table" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>

            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="employee_table" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 45px;">
                                    <input class="form-check-input" type="checkbox" id="select_all_employees">
                                </th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Reporting Manager</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.modals.hr-employees-modal')
    <style>
        .action-btns {
            display: flex;
            justify-content: center
        }
    </style>
@endsection
@section('scripts')
    @vite('resources/js/hrEmployees.js')
@endsection
