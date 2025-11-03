@extends('layouts.app')
@include('partials.general-employee-heading')
@section('headings') Payslips @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#" onclick="history.back()">Employee</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payslips</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                My Payslip Records
            </div>
            <a class="d-none" id="employee_id" href="" data-id="{{$id}}"></a>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="employeePayslipTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
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
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/employeePayslip.js') }}"></script>
@endsection
