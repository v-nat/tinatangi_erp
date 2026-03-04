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

    {{-- ── Live Table Occupancy Panel ── --}}
    <div class="card mb-4" id="occupancy-panel">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="card-title mb-0">
                        <i class="fa-solid fa-table-cells-large me-1 text-primary"></i>
                        Today's Table Occupancy
                    </h6>
                    <span id="occupancy-clock" class="badge bg-secondary fw-normal" style="font-size:.78rem;"></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">Auto-refresh in <span id="refresh-countdown" class="fw-semibold">60</span>s</small>
                    <button class="btn btn-outline-primary btn-sm" id="btn-refresh-occupancy">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>

            {{-- Legend --}}
            <div class="d-flex flex-wrap gap-3 mt-2" style="font-size:.8rem;">
                <span><span class="badge" style="background:#198754;">&#9679;</span> Active</span>
                <span><span class="badge" style="background:#dc3545;">&#9679;</span> No-Show Risk</span>
                <span><span class="badge" style="background:#0d6efd;">&#9679;</span> Upcoming</span>
                <span><span class="badge" style="background:#ffc107;color:#000;">&#9679;</span> Soon (&le;30 min)</span>
                <span><span class="badge bg-warning text-dark">&#9679;</span> Pending</span>
                <span><span class="badge bg-secondary">&#9679;</span> Free</span>
                <span><span class="badge" style="background:#6c757d;">&#9679;</span> Overdue</span>
            </div>
        </div>
        <div class="card-body">
            <div id="occupancy-container">
                <div class="text-center py-4 text-muted" id="occupancy-loading">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Loading occupancy...
                </div>
            </div>
        </div>
    </div>

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
