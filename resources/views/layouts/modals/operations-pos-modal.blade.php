<!--warning theme Modal -->
<div class="modal fade text-left" id="addItemOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="myModalLabel120">
                    Add Item to Order
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>
                    Confirm the quantity for this Item
                </h6>
                <form id="addOrder" class="mt-4">
                    @csrf
                    <input type="hidden" name="id" id="_item_id">
                    <div class="row">
                        <div class="col-7">
                            <h6 class="fw-normal">Details:</h6>
                            <h6 class="fw-normal" id="_item_name"></h6>
                            <h6 class="fw-normal" id="_base_price" data-price=""></h6>
                        </div>

                        <div class="col-5">
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" id="quantity" class="form-control py-2" min="1" required
                                    placeholder="0" name="quantity">
                                <div class="invalid-feedback">Quantity is required.</div>
                            </div>
                            <h6 class="fw-normal" id="total_price"></h6>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelAddOrder" type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>

                <button id="addOrderBtn" type="button" class="btn btn-warning ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Add</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- view modal-->
<div class="modal fade text-left w-100" id="orderTransactions" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel20" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">Recent Orders</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body p-4">
                <section class="section">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="posOrdersTransactions" class="table table-hover dataTable no-footer"
                                    style="width:100% !important; table-layout:fixed">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Items</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Payment</th>
                                            <th>Cashier</th>
                                            <th>Status</th>
                                            <th>Actions</th>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
                {{-- <button type="button" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Accept</span>
                </button> --}}
            </div>
        </div>
    </div>
</div>

<!--warning theme Modal -->
<span id="cashierNameDisplay" class="d-none">{{ auth()->user()->full_name }}</span>
<div class="modal fade text-left" id="orderFinalization" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="myModalLabel120">
                    Complete Order
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <form id="finalizeOrderForm" onsubmit="return false;">
                @csrf
                <div class="modal-body">
                    <div id="orderSummaryList" class="mb-2"></div>

                    {{-- Discount panel --}}
                    <div id="discount-panel" class="d-none mb-2 p-2 rounded" style="background:rgba(205,164,94,.08);border:1px solid rgba(205,164,94,.3)">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="discount-label" class="small fw-semibold" style="color:#cda45e"></span>
                            <button type="button" id="btn-remove-discount" class="btn btn-link btn-sm p-0 text-danger" style="font-size:.75rem">Remove</button>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>Discount</span>
                            <span id="discount-amount-display" class="fw-semibold text-success">-₱0.00</span>
                        </div>
                    </div>

                    {{-- Multiple discount chooser --}}
                    <div id="discount-chooser" class="d-none mb-2">
                        <p class="small mb-1" style="opacity:.6">Multiple discounts available — select one:</p>
                        <div id="discount-options-list" class="d-flex flex-column gap-1"></div>
                    </div>

                    {{-- Hidden discount submission fields --}}
                    <input type="hidden" id="applied_discount_id" name="discount_id" value="">
                    <input type="hidden" id="applied_discount_amount" name="discount_amount" value="0">

                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold">Total:</h5>
                        <h5 class="fw-bold" id="modalGrandTotal">₱ 0.00</h5>
                    </div>
                    <hr>
                    <h6 class="mb-2">Payment</h6>
                    <div class="form-group">
                        <label for="cashReceivedInput">Cash Received</label>
                        <input type="number" class="form-control form-control-lg" id="cashReceivedInput"
                            placeholder="Enter amount">
                    </div>
                    <div class="d-flex justify-content-between">
                        <h5 class="fw-bold">Change:</h5>
                        <h5 class="fw-bold text-success" id="modalChange">₱ 0.00</h5>
                    </div>
                    <hr>
                    <h6 class="mb-3">Select Order Type</h6>
                    <div class="btn-group w-100" role="group" aria-label="Order Type">
                        <button type="button" class="btn btn-outline-primary active order-type-btn"
                            data-type="dine-in">Dine-In</button>
                        <button type="button" class="btn btn-outline-primary order-type-btn"
                            data-type="take-out">Take-Out</button>
                    </div>
                    <input type="hidden" name="order_type" id="order_type_input" value="dine-in">
                </div>
                <div class="modal-footer">
                    <button id="cancelBtn" type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <span class="d-sm-block">Cancel</span>
                    </button>
                    <button id="closeBtn" type="button" class="btn btn-light-secondary d-none" data-bs-dismiss="modal">
                        <span class="d-sm-block">Close</span>
                    </button>
                    <button id="printReceiptBtn" type="button" class="btn btn-primary ml-1 d-none">
                        <span class="d-sm-block">Print Receipt</span>
                    </button>
                    <button id="confirmSubmitOrder" type="submit" class="btn btn-warning ml-1">
                        <span class="d-sm-block">Submit Order</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="receipt-container" class="d-none"></div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #receipt-container,
        #receipt-container * {
            visibility: visible;
        }

        #receipt-container {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
