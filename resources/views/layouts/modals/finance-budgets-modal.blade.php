<div class="modal fade text-left" id="RejectionConfirmation" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel120" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title white" id="myModalLabel120">
                    Budget Request Rejection
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>You are about to Reject this Budget Request</h6>
                <form id="rejectionForm">
                    @csrf
                    <input type="hidden" name="request_id" id="rejectionRequestId">
                    <input type="hidden" name="release_id" id="rejectionReleaseId">
                    <div class="form-group">
                        <label for="rejectionNotes">Rejection Notes (Required)</label>
                        <textarea class="form-control" id="rejectionNotes" name="notes" rows="3" required></textarea>
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
