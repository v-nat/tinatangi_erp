@extends('layouts.app')
@include('partials.crm-heading')
@section('crmBooking') active @endsection
@section('headings') Booking Management @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Bookings</li>
        </ol>
    </nav>

    <div class="section">
        <div class="card mb-4">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h6 class="card-title mb-0">All Bookings</h6>
                    <button class="btn btn-outline-primary btn-sm" id="btn-refresh-bookings">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label for="booking_status_filter" class="form-label mb-1">Filter by Status:</label>
                        <select id="booking_status_filter" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="booking_table_filter" class="form-label mb-1">Filter by Table:</label>
                        <select id="booking_table_filter" class="form-select form-select-sm">
                            <option value="">All Tables</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="booking_date_filter" class="form-label mb-1">Filter by Booking Date:</label>
                        <input type="date" id="booking_date_filter" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover dataTable no-footer" id="bookings-table" width="100%"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Booking Date</th>
                                <th>Time</th>
                                <th>Guests</th>
                                <th>Table</th>
                                <th>Status</th>
                                <th>Received</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be loaded by DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.modals.booking-modals')

@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/bookingTableManagement.js') }}"></script>
@endsection
