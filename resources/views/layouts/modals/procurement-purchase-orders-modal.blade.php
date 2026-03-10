<!-- view modal-->
<div class="modal fade text-left w-100" id="viewPO" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">View Purchase Request</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body p-4">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
                <button id="po-invoice-btn" type="button" class="btn btn-primary ml-1 d-none">
                    <span class="d-none d-sm-block">View Invoice</span>
                </button>
                <button id="po-process-btn" type="button" class="btn btn-primary ml-1 d-none">
                    <span class="d-none d-sm-block">Dispatch Order</span>
                </button>
                <button id="po-receive-btn" type="button" class="btn btn-success ml-1 d-none">
                    <span class="d-none d-sm-block">Receive Delivery</span>
                </button>
                <button id="po-redeliver-btn" type="button" class="btn btn-success ml-1 d-none">
                    <span class="d-none d-sm-block">Receive Redelivery</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receive Order Modal -->
<div class="modal fade text-left w-100" id="receiveOrderModal" tabindex="-1" role="dialog"
    aria-labelledby="receiveOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="receiveOrderModalLabel">Receive Delivery & Inspect Items</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body p-4">
                <style>
                    .return-fields {
                        display: none;
                        background-color: #fff8f8;
                        border: 1px solid #fdd;
                        padding: 10px;
                        border-radius: 5px;
                        margin-top: 10px;
                    }
                    .return-fields-redelivery {
                        display: none;
                        background-color: #fff8f8;
                        border: 1px solid #fdd;
                        padding: 10px;
                        border-radius: 5px;
                        margin-top: 10px;
                    }
                </style>

                <form id="receiveOrderForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="receive_pr_id" name="pr_id">

                    <!-- Overall Photo Section -->
                    <div class="mb-4">
                        <label for="overall_delivery_photo" class="form-label fs-6 fw-bold">Overall Delivery Photo (Proof of Receipt)</label>
                        <p class="text-muted small">Upload one photo showing all items received, the delivery vehicle, or the signed delivery note.</p>
                        <input type="file" name="overall_delivery_photo" id="overall_delivery_photo" class="form-control"
                            required accept="image/*">
                    </div>

                    <hr>

                    <!-- Items Table Section -->
                    <h5 class="mb-3">Inspect Individual Items</h5>
                    <p class="text-muted small">Enter how many units were received for each item. Any quantity short of the ordered amount will be marked as returned and requires a reason.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item Name</th>
                                    <th style="width: 120px;" class="text-center">Qty Ordered</th>
                                    <th style="width: 140px;" class="text-center">Qty Received</th>
                                    <th style="width: 120px;" class="text-center">Qty Returned</th>
                                    <th>Return Reason & Photo</th>
                                </tr>
                            </thead>
                            <tbody id="receiveItemsList">
                                <tr>
                                    <td colspan="5" class="text-center">Loading items...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>
                <button type="submit" class="btn btn-primary ml-1" id="submitReceiveOrder" form="receiveOrderForm">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Submit Inspection</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="receiveRedeliveryModal" tabindex="-1" role="dialog" aria-labelledby="receiveRedeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiveRedeliveryModalLabel">Inspect Redelivered Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="receiveRedeliveryForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="receive_redelivery_pr_id" name="pr_id">

                    <p class="text-muted small">Enter how many units were received for each redelivered item. Any quantity short of the expected redelivery amount requires a reason and will be returned again.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th style="width: 140px;" class="text-center">Originally Ordered</th>
                                    <th style="width: 160px;" class="text-center">Redelivery Expected</th>
                                    <th style="width: 150px;" class="text-center">Qty Received</th>
                                    <th style="width: 120px;" class="text-center">Qty Returned Again</th>
                                    <th>Reason for Re-Return & Photo</th>
                                </tr>
                            </thead>
                            <tbody id="redeliveryItemsList">
                                <!-- Items will be dynamically populated here -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="receiveRedeliveryForm">Confirm Inspection</button>
            </div>
        </div>
    </div>
</div>
