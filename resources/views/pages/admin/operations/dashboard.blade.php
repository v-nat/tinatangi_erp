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

    <!-- KPI Cards -->
    <section class="section row">
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
                            <h6 class="text-muted font-semibold">Orders Today</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-orders-today">Loading...</h5>
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
                            <h6 class="text-muted font-semibold">Pending Orders</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-pending-orders">Loading...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header">
                    <h4>Top 5 Selling Products (Today)</h4>
                </div>
                <div class="card-body">
                    <div id="chart-top-products"></div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-lg h-100">
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
        </div>
    </section>

    <section class="section row mt-4 mb-4">
        <div class="col-12">
            <div class="card h-100 shadow-sm border-0 rounded-lg">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Critical Stock Levels</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-low-stock">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Stock Level</th>
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
    <script typeF="module" src="{{ asset('js/operationsDashboard.js') }}"></script>
@endsection
