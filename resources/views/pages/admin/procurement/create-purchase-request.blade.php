@extends('layouts.app')
@section('title') Procurement Management @endsection
@section('sidebar-title') Procurement Management @endsection
@section('human_resources') d-none @endsection
@section('finance') d-none @endsection
@section('procurement') d-block @endsection
@section('createPR') active @endsection
@section('headings') Create Purchase Request @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('procurement.index')}}">Procurement</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Request</li>
        </ol>
    </nav>

    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" id="submitPORequest" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="order_items_payload" name="order_items_payload">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="order_id">Order ID</label>
                                            <input type="input" id="order_id" class="form-control py-3" required readonly
                                                placeholder="" name="order_id">
                                            <div class="invalid-feedback">Order ID is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="supplier">Supplier</label>
                                            <select class="form-select py-3" id="supplier" name="supplier" required>
                                                <option value="" disabled selected class="form-control py-3">Choose Supplier
                                                </option>

                                            </select>
                                            <div class="invalid-feedback">Supplier is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4.5 col-4">
                                        <div class="form-group">
                                            <label for="category">Category</label>
                                            <select class="form-select py-3" id="category" name="category" required>
                                                <option value="" disabled selected class="form-control py-3">Choose Category
                                                </option>
                                            </select>
                                            <div class="invalid-feedback">Category is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4.5 col-4">
                                        <div class="form-group">
                                            <label for="item">Item</label>
                                            <select class="form-select py-3" id="item" name="item" required>
                                                <option value="" disabled selected class="form-control py-3">Choose Item
                                                </option>
                                            </select>
                                            <div class="invalid-feedback">Item is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-4">
                                        <div class="form-group">
                                            <label for="unit">Unit</label>
                                            <input type="text" id="unit" class="form-control py-3" min=1 required readonly
                                                placeholder="n/a" name="unit">
                                            <div class="invalid-feedback">Unit is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-4">
                                        <div class="form-group">
                                            <label for="unit_price">Unit Price</label>
                                            <input type="number" id="unit_price" class="form-control py-3" min=1 required
                                                readonly placeholder="0.00" name="unit_price">
                                            <div class="invalid-feedback">Unit Price is required.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-4">
                                        <div class="form-group">
                                            <label for="qnty">Quantity</label>
                                            <input type="number" id="qnty" class="form-control py-3" min=1 required
                                                placeholder="0" name="qnty">
                                            <div class="invalid-feedback">Quantity is required.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-12 mt-2">
                                        <button id="addItem" class="btn btn-lg btn-primary">Add Item</button>
                                    </div>

                                    <div class="col-md-12 col-12 mt-5 mb-3 border border-r-8">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="orderRequest" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Item</th>
                                                        <th>Unit</th>
                                                        <th>Quantity</th>
                                                        <th>Unit Price</th>
                                                        <th>Total</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-start">
                                        <button id="submit-PO" type="submit"
                                            class="btn icon icon-left btn-primary me-1 mb-1 w-100 py-3">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            Submit Purchase Order</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
    <script src="{{ asset('js/createPurchaseRequest.js') }}   "></script>
@endsection