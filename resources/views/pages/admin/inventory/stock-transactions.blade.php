@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryTransactions') active @endsection
@section('headings') Inventory Movements @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Inventory Records</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                    <h4 class="card-title mb-0">Records Table</h4>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center" style="min-width: 240px;">
                            <label for="transaction_type_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by
                                Type:</label>
                            <select id="transaction_type_filter" class="form-select form-select-sm">
                                <option value="">All Types</option>
                            </select>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-stock-transactions">
                            <i class="fa-solid fa-rotate"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="batch_filter" class="form-label mb-1">Filter by Batch:</label>
                        <input type="text" id="batch_filter" class="form-control form-control-sm"
                            placeholder="Enter batch...">
                    </div>
                    <div class="col-md-4">
                        <label for="reference_filter" class="form-label mb-1">Filter by Reference:</label>
                        <select id="reference_filter" class="form-select form-select-sm">
                            <option value="">All References</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date_filter" class="form-label mb-1">Filter by Date:</label>
                        <input type="date" id="date_filter" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allTransactions" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Batch</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Stock</th>
                                <th>Item</th>
                                <th>Employee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    @vite('resources/js/stockTransactions.js')
@endsection
