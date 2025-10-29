<!--warning theme Modal -->
<div class="modal fade text-left" id="ApprovalConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel120" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title white" id="myModalLabel120">
                    Overtime Request Confirmation
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>
                    Are you sure you want to approve this Overtime Request?
                </h6>
                <form id="approvalForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="overtime_id" id="approvalOvertimeId">
                    <div class="form-group">
                        <label for="approvalNotes">Approval Message (Optional)</label>
                        <textarea class="form-control" id="approvalNotes" name="reason" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>

                <button id="approve-btn-confirmed" type="button" class="btn btn-warning ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Approve</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!--Danger theme Modal -->
<div class="modal fade text-left" id="RejectionConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel120" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title white" id="myModalLabel120">
                    Overtime Request Rejection
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>You are about to Reject this Overtime Request</h6>
                <form id="rejectionForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="overtime_id" id="rejectionOvertimeId">
                    <div class="form-group">
                        <label for="rejectionNotes">Rejection Notes (Optional)</label>
                        <textarea class="form-control" id="rejectionNotes" name="reason" rows="3"></textarea>
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
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modals/hr-ot-mngmnt-modal.blade.php ENDPATH**/ ?>