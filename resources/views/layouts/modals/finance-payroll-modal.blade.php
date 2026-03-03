<!-- view modal-->
<div class="modal fade text-left w-100" id="viewPayroll" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">View Payroll</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body p-4">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <span class="d-none d-sm-block">Close</span>
                </button>
                <button id="modal-reject-btn" type="button" class="btn btn-danger ml-1 d-none">
                    <span class="d-none d-sm-block">Reject</span>
                </button>
                <button id="modal-approve-btn" type="button" class="btn btn-primary ml-1 d-none">
                    <span class="d-none d-sm-block">Approve</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- payroll rejection modal -->
<div class="modal fade text-left" id="payrollRejectionModal" tabindex="-1" role="dialog"
    aria-labelledby="payrollRejectionLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title white" id="payrollRejectionLabel">
                    Payroll Request Rejection
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <h6>You are about to Reject this Payroll Request</h6>
                <form id="payrollRejectionForm">
                    @csrf
                    <input type="hidden" id="rejectionPayrollId">
                    <div class="form-group">
                        <label for="payrollRejectionNotes">Rejection Notes (Required)</label>
                        <textarea class="form-control" id="payrollRejectionNotes" name="remarks" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cancel</span>
                </button>
                <button id="payroll-reject-btn-confirmed" type="button" class="btn btn-danger ml-1">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Reject</span>
                </button>
            </div>
        </div>
    </div>
</div>
