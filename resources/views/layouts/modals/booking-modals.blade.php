<div class="modal fade" id="bookingViewModal" tabindex="-1" aria-hidden="true" aria-labelledby="bookingViewModalLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingViewModalLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Customer</h6>
                        <p class="fw-semibold mb-0" id="viewBookingName">—</p>
                        <small class="text-muted d-block" id="viewBookingContact">—</small>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Guests</h6>
                        <p class="fw-semibold mb-0" id="viewBookingGuests">—</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Status</h6>
                        <div id="viewBookingStatus">—</div>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Date</h6>
                        <p class="fw-semibold mb-0" id="viewBookingDate">—</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Time</h6>
                        <p class="fw-semibold mb-0" id="viewBookingTime">—</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Duration</h6>
                        <p class="fw-semibold mb-0" id="viewBookingDuration">—</p>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-muted mb-1">Table #</h6>
                        <p class="fw-semibold mb-0" id="viewBookingSlot">—</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Table Type</h6>
                        <p class="fw-semibold mb-0" id="viewBookingTable">—</p>
                        <small class="text-muted d-block" id="viewBookingTableDetails"></small>
                        <div class="mt-3 d-none" id="viewBookingTableImageWrapper">
                            <div class="ratio ratio-4x3">
                                <img id="viewBookingTableImage" src="" alt="Table" class="img-fluid rounded" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Customer Message</h6>
                        <p class="mb-0 text-break" id="viewBookingMessage">—</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted mb-1">Internal Note</h6>
                        <p class="mb-0 text-break" id="viewBookingNote">—</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success d-none" id="viewApproveBtn">
                    <i class="fa-solid fa-check me-1"></i> Approve
                </button>
                <button type="button" class="btn btn-danger d-none" id="viewRejectBtn">
                    <i class="fa-solid fa-xmark me-1"></i> Reject
                </button>
                <button type="button" class="btn btn-warning text-white d-none" id="viewVoidBtn">
                    <i class="fa-solid fa-ban me-1"></i> Void
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bookingApproveModal" tabindex="-1" aria-hidden="true" aria-labelledby="bookingApproveModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="bookingApproveModalLabel">Approve Booking</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveBookingForm">
                <div class="modal-body">
                    <input type="hidden" id="approveBookingId">
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Customer</h6>
                        <p class="fw-semibold mb-0" id="approveBookingCustomer">—</p>
                        <small class="text-muted d-block" id="approveBookingSchedule">—</small>
                    </div>
                    <div class="mb-3">
                        <label for="approveTableSelect" class="form-label">Assign Table</label>
                        <select id="approveTableSelect" class="form-select" required></select>
                        <div class="form-text">
                            Only active tables that can accommodate the guest count are shown.
                        </div>
                        <div class="alert alert-warning mt-3 d-none" id="approveTableAlert"></div>
                    </div>
                    <div class="mb-0">
                        <label for="approveNote" class="form-label">Internal Note (optional)</label>
                        <textarea id="approveNote" class="form-control" rows="2" maxlength="1000" placeholder="Add an internal note for this approval"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="approveBookingSubmit">
                        Approve Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="bookingRejectModal" tabindex="-1" role="dialog" aria-labelledby="bookingRejectModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="bookingRejectModalLabel">Reject Booking</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectBookingForm">
                <div class="modal-body">
                    <input type="hidden" id="rejectBookingId">
                    <h6 class="mb-3">You are about to reject this booking.</h6>
                    <p class="text-muted mb-3" id="rejectBookingSummary">—</p>
                    <div class="form-group">
                        <label for="rejectReason" class="form-label">Rejection Notes</label>
                        <textarea id="rejectReason" class="form-control" rows="3" maxlength="1000" required placeholder="Provide the reason for rejecting this booking"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger" id="rejectBookingSubmit">
                        Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="bookingVoidModal" tabindex="-1" aria-hidden="true" aria-labelledby="bookingVoidModalLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="bookingVoidModalLabel">Void Reservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="voidBookingForm">
                <div class="modal-body">
                    <input type="hidden" id="voidBookingId">
                    <p class="text-muted mb-3" id="voidBookingSummary">—</p>
                    <div class="mb-0">
                        <label for="voidReason" class="form-label">Reason for voiding</label>
                        <textarea id="voidReason" class="form-control" rows="3" maxlength="1000" required placeholder="Provide the reason for voiding this reservation"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white" id="voidBookingSubmit">
                        Void Reservation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
