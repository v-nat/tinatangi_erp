@extends('layouts.app')
@include('partials.supplier-heading')
@section('supplierDashboard') active @endsection
@section('headings') Supplier Dashboard @endsection
@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('supplier') }}">Supplier</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-end align-items-center mb-3">
        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-supplier-dashboard">
            <i class="fa-solid fa-rotate"></i> Refresh Dashboard
        </button>
    </div>

    <section class="section row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon green">
                                <i class="fa-solid fa-box"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Total Products</h6>
                            <h5 class="font-extrabold mb-0" id="summary-total-products">—</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon blue">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Active Orders</h6>
                            <h5 class="font-extrabold mb-0" id="summary-active-orders">—</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon-wrapper me-3">
                            <div class="stats-icon orange">
                                <i class="fa-solid fa-dolly"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Pending Shipments</h6>
                            <h5 class="font-extrabold mb-0" id="summary-pending-shipments">—</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <p class="text-muted mb-2">Maintain up-to-date product listings to ensure accurate purchase orders.</p>
                    <a href="#supplier-products" class="btn btn-primary btn-sm">Manage Products</a>
                </div>
            </div>
        </div>
    </section>

    <section id="supplier-products" class="section row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Products</h4>
                    <button class="btn btn-primary btn-sm" id="btn-add-supplier-product">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="supplier-products-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section row g-3 mb-4">
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header">
                    <h4 class="mb-0">Top Performing Products</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">Coming soon: sales analytics for supplier products.</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header">
                    <h4 class="mb-0">Dashboard Notes</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0" id="supplier-dashboard-notes">
                        <li class="text-muted">Use the product table to keep offerings up-to-date.</li>
                        <li class="text-muted">Monitor active orders and shipments from the summary cards.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.modals.supplier-product-modal')
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/supplierProducts.js') }}"></script>
@endsection
