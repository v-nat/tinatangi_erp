@extends('layouts.app')
@include('partials.finance-accounting-heading')
@section('financePayroll') active @endsection
@section('headings') Payroll List @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance.payroll') }}">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payroll</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center gap-3">
                    <h4 class="card-title mb-0">Payroll Table</h4>

                    <div class="d-flex align-items-center gap-3">
                        <div class="me-2">
                            <label for="payroll_department_filter" class="form-label mb-1">Department:</label>
                            <select id="payroll_department_filter" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">All Departments</option>
                                <!-- Auto-populated -->
                            </select>
                        </div>

                        <div>
                            <label for="payroll_period_filter" class="form-label mb-1">Pay Period:</label>
                            <select id="payroll_period_filter" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">All Periods</option>
                                <!-- Auto-populated -->
                            </select>
                        </div>

                        <button class="btn btn-outline-primary btn-sm" id="refresh-payrolls">
                            <i class="fa-solid fa-rotate me-1"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="payrollsTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Pay Period</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                                <th>Action</th>
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

    @include('layouts.modals.finance-payroll-modal')

    <style>
        .action-btns {
            display: flex;
            justify-content: center
        }
    </style>
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/financePayroll.js') }}   "></script>
@endsection
