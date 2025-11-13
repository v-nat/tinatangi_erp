@extends('layouts.app')
@include('partials.executives-heading')

@section('executivesDashboard') active @endsection
@section('headings') Executive Command Center @endsection

@section('content')
    <style>
        .card-surface {
            background-color: var(--bs-body-bg);
            border: 1px solid rgba(var(--bs-body-color-rgb), 0.05);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        [data-bs-theme="light"] .card-surface {
            background-color: #ffffff;
            border-color: rgba(0, 0, 0, 0.05);
        }

        [data-bs-theme="dark"] .card-surface {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .surface-segment {
            background-color: rgba(var(--bs-body-color-rgb), 0.04);
            transition: background-color 0.3s ease;
        }

        [data-bs-theme="dark"] .surface-segment {
            background-color: rgba(255, 255, 255, 0.08);
        }

        .surface-list {
            background-color: transparent;
            color: inherit;
        }
    </style>

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('executives') }}">Executives</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <section class="section" id="executives-dashboard" data-endpoint="{{ route('executives.analytics') }}"></section>

        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted fw-semibold">Revenue Today</small>
                        <h3 class="my-2" id="summary-revenue-today">Loading...</h3>
                        <span class="badge bg-success-subtle text-success">From operations</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted fw-semibold">Budget Released (MTD)</small>
                        <h3 class="my-2" id="summary-budget-released">Loading...</h3>
                        <span class="badge bg-primary-subtle text-primary">Finance</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted fw-semibold">Pending Approvals</small>
                        <h3 class="my-2" id="summary-pending-approvals">Loading...</h3>
                        <span class="badge bg-danger-subtle text-danger">Review needed</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted fw-semibold">Workforce</small>
                        <h3 class="my-2" id="summary-workforce">Loading...</h3>
                        <span class="badge bg-info-subtle text-info">Human Resources</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted fw-semibold">Inventory Alerts</small>
                        <h3 class="my-2" id="summary-inventory-alerts">Loading...</h3>
                        <span class="badge bg-warning-subtle text-warning">Low & Out of stock</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-4 col-md-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-uppercase text-muted fw-semibold">Customer Satisfaction</small>
                        <h3 class="my-2" id="summary-customer-satisfaction">Loading...</h3>
                        <span class="badge bg-secondary-subtle text-secondary">CRM feedback</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Best Sellers Trend</h5>
                            <small class="text-muted">Category champions over time</small>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted small" id="exec-best-sellers-range">Loading...</span>
                            <div class="btn-group btn-group-sm flex-wrap" role="group" aria-label="Toggle best seller period">
                                <button type="button" class="btn btn-outline-primary active" data-best-seller-toggle="weekly">
                                    Weekly
                                </button>
                                <button type="button" class="btn btn-outline-primary" data-best-seller-toggle="monthly">
                                    Monthly
                                </button>
                                <button type="button" class="btn btn-outline-primary" data-best-seller-toggle="sixMonths">
                                    Last 6 Months
                                </button>
                                <button type="button" class="btn btn-outline-primary" data-best-seller-toggle="yearly">
                                    Yearly
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart-exec-best-sellers" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Human Resources</h5>
                        <small class="text-muted">Headcount movement and pending HR actions</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Total Employees</p>
                                    <h4 class="mb-0" id="hr-total-employees">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">New Hires (30d)</p>
                                    <h4 class="mb-0" id="hr-new-hires">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Pending Leaves</p>
                                    <h4 class="mb-0" id="hr-pending-leaves">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Pending Overtime</p>
                                    <h4 class="mb-0" id="hr-pending-overtime">—</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Operations</h5>
                        <small class="text-muted">Service performance for the day</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Sales Today</p>
                                    <h4 class="mb-0" id="ops-sales-today">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Completed Orders</p>
                                    <h4 class="mb-0" id="ops-completed-orders">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Avg. Order Value</p>
                                    <h4 class="mb-0" id="ops-avg-order-value">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Orders in Progress</p>
                                    <h4 class="mb-0" id="ops-orders-in-progress">—</h4>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase small mb-2">Top Products Today</h6>
                            <ul class="list-group list-group-flush" id="ops-top-products">
                                <li class="list-group-item px-0 surface-list text-muted">Loading...</li>
                            </ul>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Units Sold Snapshot</h6>
                            <div id="chart-ops-top-products" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12 col-xl-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Finance</h5>
                        <small class="text-muted">Cash commitments and commercial health</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Net Payroll (MTD)</p>
                                    <h4 class="mb-0" id="finance-payroll-net">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Budget Released</p>
                                    <h4 class="mb-0" id="finance-budget-released">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">PO Spending</p>
                                    <h4 class="mb-0" id="finance-po-spending">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Budget Variance</p>
                                    <h4 class="mb-0" id="finance-budget-variance">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Pending Releases</p>
                                    <h4 class="mb-0" id="finance-pending-budgets">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Approved Sales (MTD)</p>
                                    <h4 class="mb-0" id="finance-sales-approved">—</h4>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h6 class="text-muted text-uppercase small mb-2">Budget vs Spend</h6>
                            <div id="chart-finance-budget-vs-spend" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Procurement</h5>
                        <small class="text-muted">Pipeline visibility and supplier activity</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Pending PRs</p>
                                    <h4 class="mb-0" id="proc-pending-prs">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Open POs</p>
                                    <h4 class="mb-0" id="proc-open-pos">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Overdue POs</p>
                                    <h4 class="mb-0" id="proc-overdue-pos">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Spend (MTD)</p>
                                    <h4 class="mb-0" id="proc-month-spend">—</h4>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Top Suppliers (MTD)</h6>
                            <ul class="list-group list-group-flush" id="proc-top-suppliers">
                                <li class="list-group-item px-0 surface-list text-muted">Loading...</li>
                            </ul>
                            <div id="chart-proc-top-suppliers" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12 col-xl-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Inventory</h5>
                        <small class="text-muted">Stock availability across the value chain</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">To Receive</p>
                                    <h4 class="mb-0" id="inventory-to-receive">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Distinct SKUs</p>
                                    <h4 class="mb-0" id="inventory-total-skus">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Low Stock</p>
                                    <h4 class="mb-0" id="inventory-low-stock">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Out of Stock</p>
                                    <h4 class="mb-0" id="inventory-out-of-stock">—</h4>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Critical Items</h6>
                            <ul class="list-group list-group-flush" id="inventory-critical-items">
                                <li class="list-group-item px-0 surface-list text-muted">Loading...</li>
                            </ul>
                            <div id="chart-inventory-alerts" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="card card-surface border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="card-title mb-0">Customer & Experience</h5>
                        <small class="text-muted">Bookings pipeline and feedback summary</small>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Upcoming Bookings</p>
                                    <h4 class="mb-0" id="crm-upcoming-bookings">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Pending Bookings</p>
                                    <h4 class="mb-0" id="crm-pending-bookings">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Pending Feedback</p>
                                    <h4 class="mb-0" id="crm-pending-feedback">—</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 surface-segment rounded">
                                    <p class="text-muted mb-1">Average Rating</p>
                                    <h4 class="mb-0" id="crm-average-rating">—</h4>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2">Next 5 Bookings</h6>
                            <ul class="list-group list-group-flush" id="crm-upcoming-list">
                                <li class="list-group-item px-0 surface-list text-muted">Loading...</li>
                            </ul>
                            <div id="chart-crm-average-rating" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('js/executivesDashboard.js') }}"></script>
@endsection
