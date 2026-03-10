@extends('layouts.app')
@include('partials.crm-heading')
@section('crmTables') active @endsection
@section('headings') Table Management @endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('crm') }}">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage Tables</li>
        </ol>
    </nav>

    <div class="section">
        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="tableManagementTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-tables-btn" data-bs-toggle="tab" data-bs-target="#tab-tables"
                    type="button" role="tab">
                    <i class="fas fa-table me-1"></i> Tables
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-locations-btn" data-bs-toggle="tab" data-bs-target="#tab-locations"
                    type="button" role="tab">
                    <i class="fas fa-map-marker-alt me-1"></i> Locations
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- Tables Tab --}}
            <div class="tab-pane fade show active" id="tab-tables" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">All Tables</h6>
                        <div>
                            <button id="btn-delete-selected-tables" class="btn btn-danger btn-sm d-none me-2">
                                <i class="fas fa-trash"></i> Delete Selected
                            </button>
                            <button class="btn btn-primary btn-sm" id="addTableBtn">
                                <i class="fas fa-plus me-1"></i> Add New Table
                            </button>
                            <button class="btn btn-outline-primary btn-sm ms-2" id="btn-refresh-tables">
                                <i class="fa-solid fa-rotate"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dataTable no-footer" id="tables-table" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Capacity</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
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

            {{-- Locations Tab --}}
            <div class="tab-pane fade" id="tab-locations" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Table Locations</h6>
                        <div>
                            <button class="btn btn-primary btn-sm" id="addLocationBtn">
                                <i class="fas fa-plus me-1"></i> Add New Location
                            </button>
                            <button class="btn btn-outline-primary btn-sm ms-2" id="btn-refresh-locations">
                                <i class="fa-solid fa-rotate"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dataTable no-footer" id="locations-table" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
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
        </div>
    </div>

    @include('layouts.modals.table-management-modal')
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/tableReservationManagement.js') }}"></script>
@endsection
