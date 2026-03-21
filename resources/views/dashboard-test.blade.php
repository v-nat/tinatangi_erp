<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mazer Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <link rel="stylesheet" href="assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
</head>


<body>

    <div class="modal fade" id="invoiceApprovalModal" tabindex="-1" aria-labelledby="invoiceApprovalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-success-subtle text-dark">
                    <h5 class="modal-title fw-bold" id="invoiceApprovalModalLabel">INVOICE APPROVAL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="invoiceApprovalForm">
                        @csrf
                        <input type="hidden" name="invoice_id" id="modal_invoice_id">

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <tbody>
                                    <tr class="table-light">
                                        <td class="px-3 py-2 fw-bold w-25">Invoice No.</td>
                                        <td class="px-3 py-2">
                                            <span id="modal_invoice_no">---</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 fw-bold w-25">Supplier Name</td>
                                        <td class="px-3 py-2">
                                            <span id="modal_supplier_name">---</span>
                                        </td>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="px-3 py-2 fw-bold w-25">Date Received</td>
                                        <td class="px-3 py-2">
                                            <input type="date" name="date_received" id="modal_date_received"
                                                class="form-control form-control-sm" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 fw-bold w-25 align-top">Items Listed</td>
                                        <td class="px-3 py-2">
                                            <div id="modal_items_listed" class="small">
                                                <p class="text-muted fst-italic mb-0">No items loaded.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="px-3 py-2 fw-bold w-25">Total Amount</td>
                                        <td class="px-3 py-2 text-primary fs-5 fw-bolder">
                                            <span id="modal_total_amount">0.00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="px-3 py-2 fw-bold w-25">Delivery Receipt No.</td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="delivery_receipt_no" id="modal_delivery_receipt_no"
                                                class="form-control form-control-sm" placeholder="Enter DR Number"
                                                required>
                                        </td>
                                    </tr>
                                    <tr class="table-light">
                                        <td class="px-3 py-2 fw-bold w-25">Approved By</td>
                                        <td class="px-3 py-2">
                                            <span id="modal_approved_by"
                                                class="text-success fw-semibold">{{ Auth::user()->name ?? 'System User' }}</span>
                                            <input type="hidden" name="approved_by_id"
                                                value="{{ Auth::user()->id ?? '' }}">
                                        </td>
                                    </tr>
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


</body>


</html>
