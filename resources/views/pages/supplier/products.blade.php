@extends('layouts.app')
@include('partials.supplier-heading')
@section('supplierProducts') active @endsection
@section('headings') My Products @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('supplier') }}">Supplier</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Products</li>
        </ol>
    </nav>

    <section class="section" id="supplier-products-section">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="mb-0">My Products</h4>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-primary btn-sm" id="btn-refresh-supplier-products">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                    <button class="btn btn-primary btn-sm" id="btn-add-supplier-product">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="bulk-action-bar" class="d-none mb-3 px-3 py-2 bg-light border rounded d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small fw-semibold me-1"><span id="bulk-selected-count">0</span> item(s) selected</span>
                    <button class="btn btn-sm btn-success" data-bulk-action="set_active"><i class="fa-solid fa-circle-check me-1"></i>Set Active</button>
                    <button class="btn btn-sm btn-secondary" data-bulk-action="set_inactive"><i class="fa-solid fa-circle-xmark me-1"></i>Set Inactive</button>
                    <button class="btn btn-sm btn-info text-white" data-bulk-action="set_perishable"><i class="fa-solid fa-clock me-1"></i>Set Perishable</button>
                    <button class="btn btn-sm btn-warning" data-bulk-action="set_not_perishable"><i class="fa-solid fa-ban me-1"></i>Set Not Perishable</button>
                    <button class="btn btn-sm btn-outline-danger ms-auto" id="btn-clear-selection">Clear</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="supplier-products-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width:40px;" class="text-center"><input type="checkbox" class="form-check-input" id="select-all-products" title="Select all"></th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Unit Price</th>
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
    </section>

    @include('layouts.modals.supplier-product-modal')
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/supplierProducts.js') }}"></script>
@endsection
