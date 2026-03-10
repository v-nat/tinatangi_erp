{{-- Leave Request Detail Modal --}}
<div class="modal fade text-left" id="leaveDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="leaveDetailModalLabel" aria-hidden="true"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">
                <h5 class="modal-title" id="leaveDetailModalLabel">
                    <i class="fa-solid fa-calendar-days me-1"></i> Leave Request Details
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                <input type="hidden" id="detail-leave-id">

                {{-- Employee Info --}}
                <div class="row mb-3">
                    <div class="col-md-5">
                        <small class="text-muted d-block">Employee</small>
                        <span id="detail-employee" class="fw-semibold"></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Department</small>
                        <span id="detail-department"></span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Position</small>
                        <span id="detail-position"></span>
                    </div>
                </div>

                <hr class="my-2">

                {{-- Dates, Days Count, Pay Type --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Start Date</small>
                        <span id="detail-start" class="fw-semibold"></span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">End Date</small>
                        <span id="detail-end" class="fw-semibold"></span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Working Days</small>
                        <span id="detail-days" class="fw-semibold"></span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Leave Pay</small>
                        <span id="detail-pay-type"></span>
                        {{-- Editable toggle — only visible for pending leaves --}}
                        <div id="pay-type-toggle" class="btn-group btn-group-sm mt-1" role="group" style="display:none;">
                            <input type="radio" class="btn-check" name="approve-pay-type" id="approve-paid" value="1">
                            <label class="btn btn-outline-success" for="approve-paid">Paid</label>
                            <input type="radio" class="btn-check" name="approve-pay-type" id="approve-unpaid" value="0">
                            <label class="btn btn-outline-warning" for="approve-unpaid">Unpaid</label>
                        </div>
                    </div>
                </div>

                {{-- Reason / Status / Attachment --}}
                <div class="row mb-2">
                    <div class="col-md-5">
                        <small class="text-muted d-block">Reason</small>
                        <span id="detail-reason"></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Status</small>
                        <span id="detail-status"></span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Attachment</small>
                        <span id="detail-attachment"></span>
                    </div>
                </div>

                {{-- Reject reason textarea (revealed when Reject is clicked) --}}
                <div id="rejectReasonSection" style="display:none;">
                    <hr>
                    <div class="form-group mb-0">
                        <label for="rejectReasonInput" class="fw-semibold">
                            Rejection Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control mt-1" id="rejectReasonInput" rows="3"
                            placeholder="State the reason for rejecting this leave request..."></textarea>
                        <div class="invalid-feedback" id="rejectReasonError">Rejection reason is required.</div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                {{-- Only shown for pending leaves --}}
                <div id="detail-pending-actions" style="display:none;">
                    <button id="detail-reject-btn" type="button" class="btn btn-danger me-1">
                        <i class="fa-solid fa-x me-1"></i> Reject
                    </button>
                    <button id="detail-confirm-reject-btn" type="button" class="btn btn-danger me-1" style="display:none;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Confirm Rejection
                    </button>
                    <button id="detail-approve-btn" type="button" class="btn btn-success">
                        <i class="fa-solid fa-check me-1"></i> Approve
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
