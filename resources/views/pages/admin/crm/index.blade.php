@extends('layouts.app')
@include('partials.crm-heading')
@section('crmIndex') active @endsection
@section('headings') Customer Relationship Dashboard @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="section">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Overall Ratings</h6>
                                <h6 class="font-extrabold mb-0"></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="fa-solid fa-comments"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Received Ratings</h6>
                                <h6 class="font-extrabold mb-0"></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Displayed Ratings</h6>
                                <h6 class="font-extrabold mb-0"></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon red">
                                    <i class="fa-solid fa-clock"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Pending Ratings</h6>
                                <h6 class="font-extrabold mb-0"></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Recent Feedback</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="recentFeedbackTable" class="table table-sm table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Message</th>
                                <th>Details</th>
                                <th>Submitted Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.modals.crm-modal')

@endsection
@section('scripts')
    {{-- <script src="{{ asset('js/customerService.js') }}"></script> --}}
@endsection
