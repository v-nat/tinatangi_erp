<!-- Invoice Approval Modal -->
<div class="modal fade" id="invoiceApprovalModal" tabindex="-1" aria-labelledby="invoiceApprovalModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success-subtle text-dark">
                <!-- Title from image: INVOICE APPROVAL -->
                <h5 class="modal-title fw-bold" id="invoiceApprovalModalLabel">INVOICE APPROVAL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="invoiceApprovalForm">
                    @csrf
                    <!-- Hidden field to store the Invoice ID -->
                    <input type="hidden" name="invoice_id" id="modal_invoice_id">

                    <!-- Table structure mimicking the image layout using Bootstrap classes -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <tbody>
                                <!-- Row 1: Invoice No. -->
                                <tr class="table-light">
                                    <td class="px-3 py-2 fw-bold w-25">Invoice No.</td>
                                    <td class="px-3 py-2">
                                        <span id="modal_invoice_no">---</span>
                                    </td>
                                </tr>
                                <!-- Row 2: Supplier Name -->
                                <tr>
                                    <td class="px-3 py-2 fw-bold w-25">Supplier Name</td>
                                    <td class="px-3 py-2">
                                        <span id="modal_supplier_name">---</span>
                                    </td>
                                </tr>
                                <!-- Row 3: Date Received -->
                                <tr class="table-light">
                                    <td class="px-3 py-2 fw-bold w-25">Date Received</td>
                                    <td class="px-3 py-2">
                                        <!-- Input field for data entry -->
                                        <input type="date" name="date_received" id="modal_date_received"
                                            class="form-control form-control-sm" required>
                                    </td>
                                </tr>
                                <!-- Row 4: Items Listed (Full width for list/details) -->
                                <tr>
                                    <td class="px-3 py-2 fw-bold w-25 align-top">Items Listed</td>
                                    <td class="px-3 py-2">
                                        <!-- Placeholder for Purchase Order Details (dynamically loaded) -->
                                        <div id="modal_items_listed" class="small">
                                            <p class="text-muted fst-italic mb-0">No items loaded.</p>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Row 5: Total Amount -->
                                <tr class="table-light">
                                    <td class="px-3 py-2 fw-bold w-25">Total Amount</td>
                                    <td class="px-3 py-2 text-primary fs-5 fw-bolder">
                                        <span id="modal_total_amount">0.00</span>
                                    </td>
                                </tr>
                                <!-- Row 6: Delivery Receipt No. -->
                                <tr>
                                    <td class="px-3 py-2 fw-bold w-25">Delivery Receipt No.</td>
                                    <td class="px-3 py-2">
                                        <!-- Input field for data entry -->
                                        <input type="text" name="delivery_receipt_no" id="modal_delivery_receipt_no"
                                            class="form-control form-control-sm" placeholder="Enter DR Number" required>
                                    </td>
                                </tr>
                                <!-- Row 7: Approved By -->
                                <tr class="table-light">
                                    <td class="px-3 py-2 fw-bold w-25">Approved By</td>
                                    <td class="px-3 py-2">
                                        <span id="modal_approved_by"
                                            class="text-success fw-semibold">{{ Auth::user()->name ?? 'System User' }}</span>
                                        <input type="hidden" name="approved_by_id" value="{{ Auth::user()->id ?? '' }}">
                                    </td>
                                </tr>
                                <!-- Row 8: Approval Date -->
                                <tr>
                                    <td class="px-3 py-2 fw-bold w-25">Approval Date</td>
                                    <td class="px-3 py-2">
                                        <span id="modal_approval_date">{{ date('Y-m-d') }}</span>
                                        <input type="hidden" name="approval_date" value="{{ date('Y-m-d H:i:s') }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="approveInvoiceBtn" class="btn btn-success fw-bold shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> Approve Invoice
                </button>
            </div>
        </div>
    </div>
</div>
