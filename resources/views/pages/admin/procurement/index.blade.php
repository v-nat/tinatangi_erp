@extends('layouts.app')
@include('partials.procurement-heading')
@section('procurementIndex') active @endsection
@section('headings') Procurement Dashboard @endsection
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">

    <section class="section row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon red">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Pending PRs</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-pending-pr">...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon orange">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Pending POs</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-pending-po">...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon blue">
                                <i class="fa-solid fa-truck-field"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Active Suppliers</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-active-suppliers">...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon green">
                                <i class="fa-solid fa-peso-sign"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Spend (This Month)</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-total-spend">...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row">
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Pending Purchase Requests</h4>
                    <a href="{{ route('procurement.createPR') }}">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-recent-prs">
                            <thead>
                                <tr>
                                    <th>Order No.</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header">
                    <h4>Purchase Orders by Status</h4>
                </div>
                <div class="card-body">
                    <div id="chart-po-status"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Top 5 Suppliers by Spend (Completed POs)</h4>
                </div>
                <div class="card-body">
                    <div id="chart-top-suppliers"></div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('js/procurementDashboard.js') }}"></script>
@endsection
