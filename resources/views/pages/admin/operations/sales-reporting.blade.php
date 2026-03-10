@extends('layouts.app')
@include('partials.operations-heading')

@section('operationsSalesReports') active @endsection
@section('headings') Sales Reporting @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('op.dashboard') }}">Operations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sales Reporting</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h4 class="mb-0">Submit Sales to Finance</h4>
                <div class="d-flex align-items-center gap-2">
                    <label for="reporting_date" class="form-label mb-0">Date:</label>
                    <input type="date" id="reporting_date" class="form-control form-control-sm"
                        value="{{ now()->format('Y-m-d') }}">
                    <button id="refresh-orders" class="btn btn-outline-primary btn-sm">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Select completed orders from today and submit them to Finance for review.
                </p>
                <div class="table-responsive mb-3">
                    <table id="orders-for-reporting" class="table table-hover align-middle" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="select-all-orders" class="form-check-input">
                                </th>
                                <th>Order #</th>
                                <th>Type</th>
                                <th>Total</th>
                                <th>Created At</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    <button id="submit-sales-report" class="btn btn-primary" disabled>
                        <i class="fa-solid fa-paper-plane"></i> Submit Selected Orders
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header d-flex justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0">Submitted Reports</h4>
                    <span class="badge bg-light text-dark" id="submitted-count">0 reports today</span>
                </div>
                <button class="btn btn-outline-primary btn-sm" id="btn-refresh-submitted-reports">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="submitted-reports" class="table table-hover align-middle" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Reported At</th>
                                <th>Reviewed At</th>
                                <th>Reviewed By</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @vite('resources/js/operationsSalesReporting.js')
@endsection
