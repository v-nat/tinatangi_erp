@extends('layouts.app')
@section('title') Finance and Accounting Management @endsection
@section('sidebar-title') Finance and Accounting Management @endsection
@section('human_resources') d-none @endsection
@section('finance') d-block @endsection
@section('procurement') d-none @endsection
@section('financeBudgets') active
@endsection
@section('headings') Budget Releasing @endsection
@section('content')
    {{-- <link rel="stylesheet" href="{{ asset('source/css/buttons.dataTables.min.css') }}"> --}}

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance.payroll') }}">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Budgets</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Approved Requests - Awaiting Budget Release</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="approvalTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Requested by</th>
                                <th>Requested at</th>
                                <th>Department</th>
                                <th>Notes</th>
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
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Released Budget History</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="historyTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Release ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Requested by</th>
                                <th>Requested at</th>
                                <th>Department</th>
                                <th>Released by</th>
                                <th>Released at</th>
                                <th class="no-print">Status</th>
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

    @include('layouts.modals.finance-budgets-modal')
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}   "></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}   "></script>
    {{-- <script src="{{ asset('source/jquery/buttons.print.min.js') }}   "></script>
    <script src="{{ asset('source/jquery/dataTables.buttons.min.js') }}   "></script> --}}
    {{--
    <script src="assets/vendors/apexcharts/apexcharts.js"></script> --}}
    <script src="{{ asset('assets/js/pages/dashboard.js') }}   "></script>

    <script src="{{ asset('assets/js/main2.js') }}   "></script>
    <script src="{{ asset('js/budgetRelease.js') }}   "></script>
@endsection