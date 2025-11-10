@extends('layouts.app')
@include('partials.operations-heading')

@section('operationsIndex') active @endsection
@section('headings') Service Operations Dashboard @endsection

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('op.dashboard') }}">Operations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <section class="section row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon green rounded-lg">
                                <i class="iconly-boldWallet"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Sales Today</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-sales-today">Loading...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon blue rounded-lg">
                                <i class="iconly-boldBuy"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Completed Orders</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-completed-orders">Loading...</h5>
                            <span class="text-muted small d-block mt-1">Total orders today: <span class="fw-semibold" id="kpi-total-orders">0</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon purple rounded-lg">
                                <i class="iconly-boldChart"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Avg. Order Value</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-avg-value">Loading...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon red rounded-lg">
                                <i class="iconly-boldTime-Circle"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Orders In Progress</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-in-progress">Loading...</h5>
                            <span class="text-muted small d-block mt-1">Voided today: <span class="fw-semibold" id="kpi-voided-orders">0</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row g-3">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-lg h-100">
                <div class="card-header">
                    <h4>Top 5 Selling Products (Today)</h4>
                </div>
                <div class="card-body">
                    <div id="chart-top-products"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-lg mb-3">
                <div class="card-header">
                    <h4>Live Order Status</h4>
                </div>
                <div class="card-body d-flex flex-column justify-content-around">

                    <div class="d-flex align-items-center p-3 bg-light-warning rounded-lg">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon warning rounded-lg">
                                <i class="iconly-boldWork"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">In Queue</h6>
                            <h4 class="font-extrabold mb-0" id="status-in-queue">0</h4>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 bg-light-info rounded-lg">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon info rounded-lg">
                                <i class="iconly-boldActivity"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">In Preparation</h6>
                            <h4 class="font-extrabold mb-0" id="status-in-prep">0</h4>
                        </div>
                    </div>

                    <div class="d-flex align-items-center p-3 bg-light-success rounded-lg">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon success rounded-lg">
                                <i class="iconly-boldTick-Square"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Ready for Pickup</h6>
                            <h4 class="font-extrabold mb-0" id="status-ready">0</h4>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header">
                    <h4>Order Mix (Today)</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group" id="order-type-list">
                        <li class="list-group-item text-center text-muted">Loading...</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section row mt-4 mb-4">
        <div class="col-12">
            <div class="card h-100 shadow-sm border-0 rounded-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Critical Stock Levels</h4>
                    <button class="btn btn-outline-primary btn-sm" id="btn-refresh-low-stock">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-low-stock">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Servings Left</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    {{-- We need these for the charts --}}
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script type="module" src="{{ asset('js/operationsDashboard.js') }}"></script>
@endsection
