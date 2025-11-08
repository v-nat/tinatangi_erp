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
        <div class="card mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">All Tables</h6>
                <button class="btn btn-primary btn-sm" id="addTableBtn">
                    <i class="fas fa-plus me-1"></i> Add New Table
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover dataTable no-footer" id="tables-table" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Description</th>
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

    @include('layouts.modals.crm-modal')
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/tableReservationManagement.js') }}"></script>
@endsection
