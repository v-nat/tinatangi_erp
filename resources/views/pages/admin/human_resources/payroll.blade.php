@extends('layouts.app')
@section('title') Human Resources Dashboard @endsection
@section('sidebar-title') Human Resources Management @endsection
@section('payroll') active
@endsection
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
                    <table id="payrollsTable" class="table table-striped table-hover" style="width:100%">
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
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}   "></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
    <script src="{{ asset('js/hrPayroll.js') }}"></script>
@endsection