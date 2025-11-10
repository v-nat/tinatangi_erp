@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryProducts') active @endsection
@section('headings') Products @endsection
@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first pt-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-last float-end d-flex justify-content-end">
                <button class="btn btn-danger" id="batchDeleteBtn" style="display: none; margin-right: 10px;">
                    Delete Selected
                </button>
            </div>
        </div>
        <section class="section mt-2">
            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h4 class="card-title mb-0">Products</h4>

                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center">
                            <label for="category_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Category:</label>
                            <select id="category_filter" class="form-select form-select-sm">
                                <option value="">All Categories</option>

                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm" id="addProductBtn">
                            <i class="fa-solid fa-plus"></i> Add New Product
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-products">
                            <i class="fa-solid fa-rotate"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="products-table" class="table table-hover dataTable no-footer"
                            style="width:100% !important;">

                        </table>
                    </div>
                </div>
            </div>
        </section>

        @include('layouts.modals.inventory-modal')
@endsection
    @section('scripts')
        <script type="module" src="{{ asset('js/inventoryProducts.js') }}"></script>
        <script type="module" src="{{ asset('js/recipeManagement.js') }}"></script>
    @endsection
