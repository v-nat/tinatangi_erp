@extends('layouts.app')
@include('partials.finance-accounting-heading')
@section('financeSales') active @endsection
@section('headings') Sales Transactions @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance') }}">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sales Transactions</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="mb-0">Reported Sales from Operations</h4>
                <div class="d-flex align-items-center gap-2">
                    <label for="sales-status-filter" class="form-label mb-0">Status:</label>
                    <select id="sales-status-filter" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="11">Pending</option>
                        <option value="23">Approved</option>
                        <option value="12">Rejected</option>
                    </select>
                    <button id="refresh-sales-reports" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="finance-sales-table" class="table table-hover align-middle" style="width: 100%;">
                        <thead>
                            <th style="width: 40px;">
                                <input type="checkbox" id="select-all-reports" class="form-check-input">
                            </th>
                            <th>Order #</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Reported By</th>
                            <th>Reported At</th>
                            <th>Reviewed By</th>
                            <th>Reviewed At</th>
                            <th>Remarks</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-end mt-3">
                    <button id="reject-sales-reports" class="btn btn-outline-danger" disabled>
                        <i class="fa-solid fa-xmark"></i> Reject Selected
                    </button>
                    <button id="approve-sales-reports" class="btn btn-success" disabled>
                        <i class="fa-solid fa-check"></i> Approve Selected
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Sales Activity (Last 7 Days)</h4>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-muted small">Pending: <span class="fw-semibold" id="sales-pending-count">0</span></div>
                    <div class="text-muted small">Approved Today: <span class="fw-semibold" id="sales-approved-today">₱
                            0.00</span></div>
                </div>
            </div>
            <div class="card-body">
                <div id="finance-sales-activity"></div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    @vite('resources/js/financeSalesTransactions.js')
@endsection
