@extends('layouts.app')
@include('partials.procurement-heading')
@section('procurementIndex') active @endsection
@section('headings') Procurement Dashboard @endsection
@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('procurement.index') }}">Procurement</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-end align-items-center mb-3">
        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-dashboard">
            <i class="fa-solid fa-rotate"></i> Refresh Dashboard
        </button>
    </div>

    {{-- ── Procurement Forecasting ── --}}
    <section class="section row g-3 mb-4">

        {{-- Section header + filters --}}
        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <h5 class="fw-semibold text-muted text-uppercase mb-0" style="letter-spacing:.05em;">
                <i class="fa-solid fa-chart-line me-2"></i>Procurement Forecasting
            </h5>
            <div class="d-flex align-items-center gap-2 flex-wrap" id="forecast-period-filters">
                {{-- Year dropdown — last 5 years --}}
                <select id="forecast-year" class="form-select form-select-sm" style="width:auto;">
                    @for ($y = now()->year; $y >= now()->year - 4; $y--)
                        <option value="{{ $y }}" @selected($y === now()->year)>{{ $y }}</option>
                    @endfor
                </select>

                {{-- Month range — From --}}
                @php
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                @endphp
                <select id="forecast-month-start" class="form-select form-select-sm" style="width:auto;">
                    @foreach ($months as $mi => $mn)
                        <option value="{{ $mi + 1 }}">{{ $mn }}</option>
                    @endforeach
                </select>
                <span class="text-muted small">to</span>
                <select id="forecast-month-end" class="form-select form-select-sm" style="width:auto;">
                    @foreach ($months as $mi => $mn)
                        <option value="{{ $mi + 1 }}" @selected($mi + 1 === 12)>{{ $mn }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Combined: Forecast KPI + Top Re-ordered Items --}}
        <div class="col-12 col-xl-4">
            <div class="card h-100 shadow-sm border-0">

                {{-- KPI section --}}
                <div class="card-body border-bottom pb-3">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon blue">
                                <i class="fa-solid fa-crystal-ball"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Forecasted Spend</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-forecast-spend">—</h5>
                            <span class="text-muted small" id="kpi-forecast-month"></span>
                        </div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">
                        <i class="fa-solid fa-circle-info me-1"></i>
                        <span id="forecast-avg-note">2-quarter moving average of completed PO spend.</span>
                    </p>
                </div>

                {{-- Top Re-ordered Items --}}
                <div class="card-header border-bottom py-2">
                    <h4 class="mb-0">Top Re-ordered Items</h4>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush" id="top-reordered-items">
                        <li class="list-group-item text-muted">Loading...</li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- Spend Trend Chart (wider) --}}
        <div class="col-12 col-xl-8">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="mb-0">Spend Trend</h4>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted small" id="forecast-chart-subtitle">Quarterly view</span>
                        <div class="btn-group btn-group-sm" id="chart-view-toggle" role="group" aria-label="Chart view">
                            <button type="button" class="btn btn-primary active" data-view="period">Period</button>
                            <button type="button" class="btn btn-outline-primary" data-view="yearly">Yearly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-monthly-spend"></div>
                </div>
            </div>
        </div>

    </section>

    <section class="section row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon red">
                                <i class="fa-solid fa-file-circle-question"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Pending Purchase Requests</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-pending-pr">—</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon orange">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Open Purchase Orders</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-open-po">—</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon blue">
                                <i class="fa-solid fa-truck-ramp-box"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Overdue Deliveries</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-overdue-po">—</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon green">
                                <i class="fa-solid fa-peso-sign"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Spend This Month</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-month-spend">—</h5>
                            <span class="text-muted small">Completed purchase orders</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row g-3 mb-4">
        <div class="col-12 col-xl-7">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Latest Purchase Requests</h4>
                    <a href="{{ route('procurement.createPR') }}" class="text-decoration-none">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Request #</th>
                                    <th>Requested On</th>
                                    <th>Requester</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent-prs-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header">
                    <h4 class="mb-0">Open Purchase Orders</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Supplier</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent-pos-body">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row g-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header">
                    <h4 class="mb-0">Top Suppliers (This Month)</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="top-suppliers-list">
                        <li class="list-group-item text-muted">Loading...</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header">
                    <h4 class="mb-0">Procurement Snapshot</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0" id="dashboard-summary">
                        <li class="text-muted">Dashboard summary will appear here.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('js/procurementDashboard.js') }}"></script>
@endsection
