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
                <h6 class="card-title">All Bookings</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover dataTable no-footer" id="bookings-table" width="100%"
                        cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Booking Date</th>
                                <th>Booking Time</th>
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

@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/bookingTableManagement.js') }}"></script>
@endsection
