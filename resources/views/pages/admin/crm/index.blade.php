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
            <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="col-12 col-sm-6 col-lg-3">
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
            <div class="col-12 col-sm-6 col-lg-3">
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
                                    <!-- Data will be populated by JS -->
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
@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('js/crmDashboard.js') }}"></script>
@endsection
