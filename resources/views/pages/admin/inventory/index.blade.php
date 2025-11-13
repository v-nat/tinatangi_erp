@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryIndex') active @endsection
@section('headings') Inventory @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <style>
        .best-seller-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: flex-start;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            background: rgba(15, 23, 42, 0.65);
            z-index: 1050;
            overflow-y: auto;
        }

        .best-seller-overlay.active {
            display: flex;
        }

        .best-seller-overlay-panel {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
            max-width: 1200px;
            width: 100%;
            position: relative;
        }

        .best-seller-overlay-close {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        body.best-seller-overlay-open {
            overflow: hidden;
        }

        @media (max-width: 576px) {
            .best-seller-overlay {
                padding: 2rem 1rem;
            }
        }
    </style>


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
                            <h5 class="font-extrabold mb-0" id="toRecieveCount"></h5>
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
                            <h6 class="text-muted font-semibold">Total Items</h6>
                            <h5 class="font-extrabold mb-0" id="totalStocksCount"></h5>
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
                            <h5 class="font-extrabold mb-0" id="lowStocksCount"></h5>
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
                            <h5 class="font-extrabold mb-0" id="outOfStockCount"></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section mt-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Best Sellers</h4>
                <p class="text-muted small mb-0">These cards spotlight the current category leaders by units sold.</p>
            </div>
            <div class="text-muted small" id="inventory-best-seller-range">Loading...</div>
        </div>
        <div class="row g-3" id="inventory-best-seller-summary">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 best-seller-summary-card" data-mode="weekly" data-has-data="false" style="cursor: pointer;">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-primary mb-2">Weekly Leader</span>
                                <h5 class="mb-1" id="inventory-weekly-title">Loading...</h5>
                                <div class="text-muted small" id="inventory-weekly-category">-</div>
                            </div>
                            <div class="position-relative" style="width:72px; height:72px;">
                                <img id="inventory-weekly-image" src="/logo.png" alt="Weekly best seller" class="rounded-circle w-100 h-100 object-fit-cover border border-2 border-light" />
                                <span class="badge bg-success position-absolute top-0 start-0 translate-middle rounded-pill px-2 py-1">#<span id="inventory-weekly-rank">1</span></span>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Units Sold</span>
                                <span class="fw-semibold" id="inventory-weekly-units">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Total Revenue</span>
                                <span class="fw-semibold" id="inventory-weekly-revenue">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100 best-seller-summary-card" data-mode="monthly" data-has-data="false" style="cursor: pointer;">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-secondary mb-2">Monthly Leader</span>
                                <h5 class="mb-1" id="inventory-monthly-title">Loading...</h5>
                                <div class="text-muted small" id="inventory-monthly-category">-</div>
                            </div>
                            <div class="position-relative" style="width:72px; height:72px;">
                                <img id="inventory-monthly-image" src="/logo.png" alt="Monthly best seller" class="rounded-circle w-100 h-100 object-fit-cover border border-2 border-light" />
                                <span class="badge bg-success position-absolute top-0 start-0 translate-middle rounded-pill px-2 py-1">#<span id="inventory-monthly-rank">1</span></span>
                            </div>
                        </div>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">Units Sold</span>
                                <span class="fw-semibold" id="inventory-monthly-units">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Total Revenue</span>
                                <span class="fw-semibold" id="inventory-monthly-revenue">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="inventory-best-seller-overlay" class="best-seller-overlay" tabindex="-1" aria-hidden="true">
        <div class="best-seller-overlay-panel p-4 p-md-5" role="dialog" aria-modal="true" aria-labelledby="inventory-best-seller-overlay-title">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                <div>
                    <h4 class="mb-1" id="inventory-best-seller-overlay-title">Best Sellers</h4>
                    <p class="text-muted small mb-0" id="inventory-best-seller-overlay-subtitle">Category leaders sorted by total units sold</p>
                </div>
                <button type="button" class="btn btn-light border-0 best-seller-overlay-close shadow-sm" data-overlay-dismiss aria-label="Close best seller overlay">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="row g-3" id="inventory-best-seller-overlay-grid">
                <!-- Cards injected via JS -->
            </div>
        </div>
    </div>

    <section class="section row my-4">
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Recent Items</h4>
                <button class="btn btn-outline-primary btn-sm" id="btn-refresh-recent-items">
                    <i class="fa-solid fa-rotate"></i> Refresh
                </button>
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
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.js') }}"></script>
    <script type="module" src="{{ asset('js/inventoryDashboard.js') }}"></script>
@endsection
