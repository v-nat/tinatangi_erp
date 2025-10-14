<!--warning theme Modal -->
<div class="modal fade text-left" id="addItemOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
    aria-hidden="true">
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
