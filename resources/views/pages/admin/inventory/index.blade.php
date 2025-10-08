@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryIndex') active
@endsection
@section('headings') Inventory @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>


    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon green">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">To Receive </h6>
                            <h6 class="font-extrabold mb-0" id="toRecieveCount"></h6>
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
                            <div class="stats-icon blue">
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Total Stocks</h6>
                            <h6 class="font-extrabold mb-0" id="totalStocksCount"></h6>
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
                            <div class="stats-icon bg-warning">
                                <i class="fa-solid fa-arrow-trend-down"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Low Stocks</h6>
                            <h6 class="font-extrabold mb-0" id="lowStocksCount"></h6>
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
                                <i class="fa-solid fa-exclamation"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Out of Stock</h6>
                            <h6 class="font-extrabold mb-0" id="outOfStockCount"></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section row mb-2">
        <div class="col-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">To Receive Items</h4>
                </div>
                <div id="invClaims" class="card-body">
                    `<div class="alert alert-light-success">No purchase requests are currently ready for receiving.</div>`
                </div>
            </div>

        </div>

        <div class="col-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Need for Restock</h4>
                </div>
                <div id="invRestock" class="card-body">
                    <div class="alert alert-light-warning">No items are currently low in stocks.</div>
                </div>
            </div>

        </div>
    </section>


    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Recent Items</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="recentItems" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>SKU</th>
                                <th>Item Name</th>
                                <th>Unit</th>
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

    @include('layouts.modals.inventory-modal')
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/inventoryDashboard.js') }}"></script>
@endsection
