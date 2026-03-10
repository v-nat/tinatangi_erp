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

    <section class="section row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h4 class="card-title mb-1">Approved Requests</h4>
                            <p class="text-muted small mb-0">
                                These requests are ready for disbursement. Review the details and release when appropriate.
                            </p>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" id="refresh-approvals">
                            <i class="fa-solid fa-rotate me-1"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive rounded-3">
                        <table id="approvalTable" class="table table-hover align-middle mb-0"
                            style="width:100% !important; table-layout:fixed">
                            <thead>
                                <th>#</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Requested by</th>
                                <th>Requested at</th>
                                <th>Department</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title mb-1">Budget Releases Overview</h4>
                    <p class="text-muted small mb-0">
                        Track the volume of approved requests versus released amounts over time.
                    </p>
                </div>
                <div class="card-body">
                    <div id="budget-release-chart" class="w-100"></div>
                    <p class="text-muted small mb-0 mt-3">
                        Green bars indicate releases; blue bars show approvals. Aim to keep releases aligned with approvals.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section mt-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header border-0 pb-0">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="card-title mb-1">Released Budget History</h4>
                        <p class="text-muted small mb-0">
                            Browse the release trail to see who approved what and when. Use the filter to narrow by type.
                        </p>
                    </div>

                    <div class="d-flex align-items-center" style="width: 320px;">
                        <label for="pr_finance_type_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by
                            Type:</label>
                        <select id="pr_finance_type_filter" class="form-select form-select-sm">
                            <option value="">All Types</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive rounded-3">
                    <table id="historyTable" class="table table-hover align-middle mb-0"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
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
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script type="module" src="{{ asset('js/budgetRelease.js') }}"></script>
@endsection

@push('styles')
    <style>
        #budget-release-chart {
            min-height: 260px;
        }

        #approvalTable tbody tr:hover,
        #historyTable tbody tr:hover {
            background-color: rgba(25, 135, 84, 0.05) !important;
        }

        .card .card-header .card-title {
            font-weight: 600;
        }
    </style>
@endpush
