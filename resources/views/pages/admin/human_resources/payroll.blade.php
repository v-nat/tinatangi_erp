@extends('layouts.app')
@include('partials.human-resources-heading')
@section('payroll') active @endsection
@section('headings') Payroll List @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payroll</li>
        </ol>
    </nav>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Payroll Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="payrollsTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Pay Period</th>
                                <th>Regular Pay</th>
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

    @include('layouts.modals.hr-payroll-modal')

    <style>
        .action-btns {
            display: flex;
            justify-content: center
        }
    </style>
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/hrPayroll.js') }}"></script>
@endsection
