@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryItems') active @endsection
@section('headings') Inventory @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Items</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Items Table</h4>

                <div class="d-flex align-items-center" style="width: 320px;">
                    <label for="category_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Category:</label>
                    <select id="category_filter" class="form-select form-select-sm">
                        <option value="">All Categories</option>

                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allInventoryItems" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SKU</th>
                                <th>Item Name</th>
                                <th>Unit</th>
                                <th>Location</th>
                                <th>Category</th>
                                <th>Stocks</th>
                                <th>Cost Price</th>
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
    </section>
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/inventoryAllItems.js') }}"></script>
@endsection
