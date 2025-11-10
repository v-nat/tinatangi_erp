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
                <div class="table-responsive">
                    <table class="table table-hover" id="supplier-products-table" style="width: 100%;">
                        <thead>
                            <tr>
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
