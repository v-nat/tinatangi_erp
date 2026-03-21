@extends('layouts.app')
@include('partials.crm-heading')
@section('crmIndex') active @endsection
@section('headings') Customer Relationship Dashboard @endsection
@section('content')
    <link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <section class="section mb-4">
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-lg">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon-wrapper me-3">
                                <div class="stats-icon orange">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Average Rating</h6>
                                <h5 class="font-extrabold mb-0" id="kpi-average-rating">...</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon-wrapper me-3">
                                <div class="stats-icon purple">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Upcoming Bookings</h6>
                                <h5 class="font-extrabold mb-0" id="kpi-upcoming-bookings">...</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon-wrapper me-3">
                                <div class="stats-icon blue">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Pending Approvals</h6>
                                <h5 class="font-extrabold mb-0" id="kpi-pending-bookings">...</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon-wrapper me-3">
                                <div class="stats-icon red">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Feedback Pending</h6>
                                <h5 class="font-extrabold mb-0" id="kpi-feedback-pending">...</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon-wrapper me-3">
                                <div class="stats-icon green">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-muted font-semibold">Active Promotions</h6>
                                <h5 class="font-extrabold mb-0" id="kpi-active-announcements">...</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section mb-4">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Booking Trend (Last 30 Days)</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-bookings-trend"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Booking Status Mix</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-booking-status"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section mb-4">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">7-Day Booking Forecast</h4>
                        <span class="badge bg-warning text-dark">
                            <i class="fa-solid fa-chart-line me-1"></i>Predictive
                        </span>
                    </div>
                    <div class="card-body">
                        <div id="chart-booking-forecast"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Forecast Insights</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6 border-end">
                                <div class="text-muted small">Peak Day</div>
                                <div class="fw-bold" id="forecast-peak-day">—</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Weekly Total</div>
                                <div class="fw-bold" id="forecast-total-week">—</div>
                            </div>
                        </div>
                        <hr class="mt-0">
                        <div id="forecast-insights-list"></div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            Based on same-day-of-week averages over the last 12 weeks.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section mb-4">
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Next Bookings</h4>
                        <a href="{{ url('/customer-service/bookings') }}" class="btn btn-outline-primary btn-sm">
                            Manage Bookings
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="table-upcoming-bookings">
                                <thead>
                                    <tr>
                                        <th>Guest</th>
                                        <th>Guests</th>
                                        <th>Schedule</th>
                                        <th>Table</th>
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
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Customer Feedback Insights</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-category-ratings" class="mb-4"></div>
                        <div id="chart-ratings-distribution"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section mb-4">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Top Rated Feedback</h4>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" id="list-top-feedback">
                            <li class="list-group-item text-center text-muted">Loading top feedback…</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Recent Feedback</h4>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" id="list-recent-feedback">
                            <li class="list-group-item text-center text-muted">Loading recent feedback…</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    @vite('resources/js/crmDashboard.js')
@endsection
