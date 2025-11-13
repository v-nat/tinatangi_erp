@extends('layouts.modules-layouts.operations-screens-layout')
@section('title') Point of Sale System | Tinatangi Cafe @endsection
@section('topTitle') Tinatangi Cafe | Point of Sale @endsection
@section('screen') POS @endsection
@section('posTopNav') active @endsection
@section('navSearch')
    <div class="pos-search-wrapper mx-auto">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="fa fa-search text-muted"></i>
            </span>
            <input type="text" id="product-search-input" class="form-control border-start-0"
                placeholder="Search products...">
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid p-0">

        <div class="row align-items-center min-vh-90 g-2 justify-content-center">

            <div class="col-12 col-md-9 d-flex">
                <div class="card w-100">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0"></h4>
                        <div class="d-flex align-items-center gap-2">
                            <label for="pos-sort-select" class="mb-0 text-muted small fw-semibold">Sort by</label>
                            <select id="pos-sort-select" class="form-select form-select-sm w-auto">
                                <option value="default" selected>Default</option>
                                <option value="alphabetical">Alphabetical (A-Z)</option>
                                <option value="servings">Servings (High-Low)</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
                                <div class="pos-category-nav shadow-sm p-2 rounded">
                                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                        aria-orientation="vertical">
                                        <div class="text-center py-3 text-muted" id="category-placeholder">
                                            Loading categories...
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-10 overflow-y-auto vh-80">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="text-center py-5 text-muted" id="products-placeholder">
                                        Select a category to view products.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-2 d-flex overflow-y-auto vh-90">
                <div class="card w-100">
                    <div class="card-header">
                        <h2 class="card-title">Order</h2>
                    </div>

                    <div id="orderList" class="card-body overflow-y-auto">

                        <div id="order-placeholder" class="col-12 text-center my-5 p-5">
                            <h6 class="text-muted">No items added to the order yet.</h6>
                            <p class="text-muted">New orders will appear here automatically.</p>
                        </div>

                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title mb-3">Total Cost:</h6>
                            <h6 class="mb-3" id="order-total-amount">₱ 0.00</h6>
                        </div>

                        <button id="submit-order-btn" class="btn btn-lg btn-primary w-100 -mt-2">
                            Complete Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.modals.operations-pos-modal')

    <style>
        .col-md-2 {
            width: 20%;
        }

        .min-vh-90 {
            min-height: 90vh;
        }

        .vh-90 {
            height: 91.5vh;
        }

        .vh-80 {
            height: 77vh;
        }

        .pos-category-nav {
            max-height: 77vh;
            overflow-y: auto;
            background-color: #ffffff;
        }

        .pos-search-wrapper {
            width: 100%;
            max-width: 380px;
        }

        .pos-search-wrapper .form-control:focus {
            box-shadow: none;
        }

        .product-card-fixed-size {
            position: relative; /* MODIFICATION: Added for servings overlay */
            height: 250px;
            width: 250px;
        }

        .product-card-fixed-size .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .product-card-fixed-size .prod-price {
            margin-top: auto;
            font-size: 1rem;
            font-weight: 700;
        }

        .product-card-fixed-size .prod-name {
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {

            .product-card-fixed-size {
                height: 220px;
                width: 100%;
            }

            .product-card-fixed-size .card-img-top {
                height: 150px;
            }

            .prod-name {
                font-size: 12px
            }
        }

        @media (max-width: 1080px) {

            .product-card-fixed-size {
                height: 170px;
                width: 170px;
            }

            .product-card-fixed-size .card-img-top {
                height: 120px;
            }

            .prod-name {
                font-size: 14px
            }
        }
    </style>
@endsection
@section('scripts')
    <script>
        const DEFAULT_PRODUCT_IMAGE = "{{ asset('img/default-product.png') }}";
    </script>
    <script type="module" src="{{ asset('js/pointOfSale.js') }}   "></script>
@endsection
