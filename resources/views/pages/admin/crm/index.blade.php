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

    <section class="section row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon purple">
                                <i class="fa-solid fa-star"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Average Rating</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-avg-rating">...</h5>
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
                                <i class="fa-solid fa-chart-simple"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Total Received</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-total-feedback">...</h5>
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
                                <i class="fa-solid fa-eye"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Displayed on Site</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-displayed-count">...</h5>
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
                            <div class="stats-icon red">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted font-semibold">Pending Review</h6>
                            <h5 class="font-extrabold mb-0" id="kpi-pending-count">...</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Feedback Trend (Last 30 Days)</h4>
                </div>
                <div class="card-body">
                    <div id="chart-feedback-trend"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Avg. Rating by Category</h4>
                </div>
                <div class="card-body">
                    <div id="chart-category-ratings"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Recent Feedback Awaiting Review</h4>
                    <a href="{{ route('crm.feedback') }}">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="table-recent-pending">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Message</th>
                                    <th>Submitted Date</th>
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
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4>Ratings Distribution</h4>
                </div>
                <div class="card-body">
                    <div id="chart-ratings-distribution"></div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/pages/ui-apexchart.js') }}"></script>
    <script src="{{ asset('js/crmDashboard.js') }}"></script>
@endsection
