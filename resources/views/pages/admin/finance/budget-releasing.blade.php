@extends('layouts.app')
@include('partials.finance-accounting-heading')
@section('financeBudgets') active @endsection
@section('headings') Budget Releasing @endsection
@section('content')
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
                    <table id="approvalTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title">Released Budget History</h4>

                    <div class="d-flex align-items-center" style="width: 320px;">
                        <label for="pr_finance_type_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by
                            Type:</label>
                        <select id="pr_finance_type_filter" class="form-select form-select-sm">
                            <option value="">All Types</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="historyTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
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
    <script type="module" src="{{ asset('js/budgetRelease.js') }}   "></script>
@endsection
