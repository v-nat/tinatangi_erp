<!--Danger theme Modal -->
<div class="modal fade text-left" id="RejectionConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel120" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title white" id="myModalLabel120">
                    Purchase Order Rejection
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>You are about to Reject this Purchase Order</h6>
                <form id="rejectionForm">
                    @csrf
                    <input type="hidden" name="req_id" id="rejectionReqId">
                    <div class="form-group">
                        <label for="rejectionNotes">Rejection Notes (Required)</label>
                        <textarea class="form-control" id="rejectionNotes" name="reason" required rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>
                <button id="reject-btn-confirmed" type="button" class="btn btn-danger ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Reject</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ////////////////////////////////////////// APPROVE WITH EXPIRATION DATES ////////////////////////////////////////// --}}

<div class="modal fade" id="approveOrderModal" tabindex="-1" role="dialog" aria-labelledby="approveOrderModalLabel"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white" id="approveOrderModalLabel">
                    <i class="fa-solid fa-truck me-2"></i> Approve &amp; Ship Order
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">You are about to approve this purchase order and ship it to Tinatangi Cafe.
                    For perishable items, you may optionally enter an expiration date.</p>
                <input type="hidden" id="approveOrderId">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th class="text-end">Qty</th>
                                <th>Expiration Date <span class="text-muted fw-normal">(perishable items only)</span></th>
                            </tr>
                        </thead>
                        <tbody id="approveItemsList">
                            <tr><td colspan="4" class="text-center text-muted">Loading items...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <span class="d-none d-sm-block">Cancel</span>
                </button>
                <button id="confirmApproveBtn" type="button" class="btn btn-primary ml-1">
                    <i class="fa-solid fa-truck me-1"></i>
                    <span class="d-none d-sm-block">Confirm &amp; Ship</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- view modal-->
<div class="modal fade text-left w-100" id="viewPO" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">View Order</h4>
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
                {{-- <button type="button" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Accept</span>
                </button> --}}
            </div>
        </div>
    </div>
</div>
