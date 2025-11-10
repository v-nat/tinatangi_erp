@extends('layouts.app')
@include('partials.supplier-heading')
@section('supplierDashboard') active @endsection
@section('headings') Supplier Dashboard @endsection
@section('content')
    <div id="supplier-dashboard-root">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ route('supplier') }}">Supplier</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-end align-items-center mb-3">
            <button class="btn btn-outline-primary btn-sm" id="btn-refresh-supplier-dashboard">
                <i class="fa-solid fa-rotate"></i> Refresh Dashboard
            </button>
        </div>

        <section class="section row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon-wrapper me-3">
                                <div class="stats-icon green">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Total Products</h6>
                                <h5 class="font-extrabold mb-0" id="summary-total-products">—</h5>
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
                                    <i class="fa-solid fa-truck"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Active Orders</h6>
                                <h5 class="font-extrabold mb-0" id="summary-active-orders">—</h5>
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
                                    <i class="fa-solid fa-dolly"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Pending Shipments</h6>
                                <h5 class="font-extrabold mb-0" id="summary-pending-shipments">—</h5>
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
                                <div class="stats-icon red">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Returns Pending</h6>
                                <h5 class="font-extrabold mb-0" id="summary-returns">—</h5>
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
                                <div class="stats-icon purple">
                                    <i class="fa-solid fa-boxes-packing"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1">Redeliveries</h6>
                                <h5 class="font-extrabold mb-0" id="summary-redeliveries">—</h5>
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
                        <h4 class="mb-0">Recent Purchase Requests</h4>
                        <small class="text-muted">Latest status updates</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Request #</th>
                                        <th>Requested On</th>
                                        <th>Status</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-orders-body">
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Loading...</td>
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
                        <h4 class="mb-0">Order Status Overview</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="status-breakdown-list">
                            <li class="list-group-item text-muted">Loading...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="section row g-3">
            <div class="col-12 col-xl-7">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header">
                        <h4 class="mb-0">Upcoming Deliveries</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>PO #</th>
                                        <th>Target Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="upcoming-deliveries-body">
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Loading...</td>
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
                        <h4 class="mb-0">Recent Activity</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="supplier-activity-feed">
                            <li class="list-group-item text-muted">Gathering activity...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/supplierDashboard.js') }}"></script>
@endsection
