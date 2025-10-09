<!-- view modal-->
<div class="modal fade text-left w-100" id="viewInvoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">Review Order</h4>
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
                <button id="receiveItem" type="button" class="btn btn-success ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Receive</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!--warning theme Modal -->
<div class="modal fade text-left" id="stockRequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="myModalLabel120">
                    Request Stock
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>
                    Send Stock Request to Procurement Department
                </h6>
                <form id="restockReqForm" class="mt-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="item_id" id="req_item_id">
                    <input type="hidden" name="sku" id="req_sku">
                    <div class="row">
                        <div class="col-7">
                            <h6 class="fw-normal">Details:</h6>
                            <h6 class="fw-normal" id="req_item_name"></h6>
                            <h6 class="fw-normal" id="req_unit_price"></h6>
                            <h6 class="fw-normal" id="req_unit"></h6>
                        </div>

                        <div class="col-5">
                            <div class="form-group">
                                <label for="qnty">Quantity</label>
                                <input type="number" id="qnty" class="form-control py-2" min="1" required
                                    placeholder="0" name="qnty">
                                <div class="invalid-feedback">Quantity is required.</div>
                            </div>
                            <h6 class="fw-normal" id="total_price"></h6>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelStockReq" type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>

                <button id="submit-req-btn" type="button" class="btn btn-warning ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Submit</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modals/inventory-modal.blade.php ENDPATH**/ ?>