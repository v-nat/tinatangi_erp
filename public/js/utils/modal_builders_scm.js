export function buildInvoiceModal(data) {
    const employee = new Set();
    let allDetailRowsHtml = "";
    let itemIndex = 0;

    if (data.purchase_orders && data.purchase_orders.length > 0) {
        data.purchase_orders.forEach((order) => {
            employee.add(order.created_by_id || "N/A");

            const details = order.details || [];

            if (details.length > 0) {
                details.forEach((item) => {
                    itemIndex++;
                    allDetailRowsHtml += `
                    <tr>
                        <td>${itemIndex}</td>
                        <td>${item.item_name || "N/A"}</td>
                        <td>${item.item_unit_name || "N/A"}</td>
                        <td class="text-end">₱${parseFloat(
                            item.unit_price || 0
                        ).toFixed(2)}</td>
                        <td class="text-end">${item.quantity || 0} ${
                        item.item_unit || "N/A"
                    }</td>
                        <td class="text-end">₱${parseFloat(
                            item.total_amount || 0
                        ).toFixed(2)}</td>
                    </tr>
                    `;
                });
            }
        });
    }

    const employeeName = Array.from(employee).join(", ");

    if (allDetailRowsHtml === "") {
        allDetailRowsHtml = `<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>`;
    }

    const html = `
    <div class="row mb-4 p-3">
        <!-- Invoice Header -->
        <div class="col-md-6">
            <p class="mb-0">Requested by: ${employeeName || "N/A"}</p>
            <p class="mb-0">Supplier: ${data.supplier_name}</p>
            <p class="mb-0">Delivery #: ${data.delivery_no || "N/A"}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <p id="view_invoice_number" class="mb-0">Invoice #: ${data.id || "N/A"}</p>
            <p class="mb-0">Date Approved: ${data.date_approved || "N/A"}</p>
            <p class="mb-0">Approved By: ${data.approved_by_id || "N/A"}</p>
        </div>
    </div>

    <hr class="mt-0">

    <div class="px-3">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover dataTable no-footer">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    ${allDetailRowsHtml}
                    <tr>
                        <td colspan="5" class="text-end"><strong>Total Amount:</strong></td>
                        <td colspan="5" class="text-end"><strong>₱${parseFloat(
                            data.total_amount || 0
                        ).toFixed(2)}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <style>
    .table-sm td,
    .table-sm th {
        padding: 0.4rem 0.6rem;
        font-size: 0.875rem;
    }
    </style>
    `;

    $("#LoadingScreen").fadeOut(200);
    $("#viewInvoice .modal-body").html(html);
    $("#viewInvoice").modal("show");
}

export function buildPOmodal(data) {
    let allDetailRowsHtml = "";
    let itemIndex = 0;

    if (data.purchase_orders && data.purchase_orders.length > 0) {
        data.purchase_orders.forEach((order) => {
            const details = order.details || [];

            if (details.length > 0) {
                details.forEach((item) => {
                    itemIndex++;
                    allDetailRowsHtml += `
                        <tr>
                            <td>${itemIndex}</td>
                            <td>${item.item_name || "N/A"}</td>
                            <td>${item.item_unit_name || "N/A"}</td>
                            <td class="text-end">₱${parseFloat(
                                item.unit_price || 0
                            ).toFixed(2)}</td>
                            <td class="text-end">${item.quantity || 0} ${
                        item.item_unit || "N/A"
                    }</td>
                            <td class="text-end">₱${parseFloat(
                                item.total_amount || 0
                            ).toFixed(2)}</td>
                        </tr>
                    `;
                });
            }
        });
    }

    if (allDetailRowsHtml === "") {
        allDetailRowsHtml = `<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>`;
    }

    const html = `
        <div class="row mb-4 p-3">
            ${data.status}
            <!-- Purchase Request Header -->
            <div class="col-md-6">
                <h6 class="mb-0">Requested By: <strong>${
                    data.requested_by_id || "N/A"
                }</strong></h6>
                <p class="mb-0">Department: ${data.department || "N/A"}</p>
                <p class="mb-0">Supplier: <strong class="text-success">${
                    data.supplier_name
                }</strong></p> <!-- SUPPLIER MOVED HERE -->
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="mb-0">Purchase Request ID: <strong>${
                    data.id || "N/A"
                }</strong></h6>
                <p class="mb-0">Requested Date: ${
                    data.requested_date || "N/A"
                }</p>
                <p class="mb-0">Total PR Amount: <strong class="text-primary">₱${parseFloat(
                    data.total_amount || 0
                ).toFixed(2)}</strong></p>
            </div>
            <div class="col-md-12 mt-3">
                <p class="mb-0">Remarks: <em>${data.remarks || "None"}</em></p>
            </div>
        </div>

        <hr class="mt-0">

        <div class="px-3">
            <h5 class="mb-3 text-primary">All Associated Line Items</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover dataTable no-footer">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Unit</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${allDetailRowsHtml}
                    </tbody>
                </table>
            </div>
        </div>

        <style>
        .table-sm td,
        .table-sm th {
            padding: 0.4rem 0.6rem;
            font-size: 0.875rem;
        }
        </style>
    `;

    $("#LoadingScreen").fadeOut(200);
    $("#viewPO .modal-body").html(html);
    $("#viewPO").modal("show");
}

export function printInvoice() {
    const $viewInvoiceModal = $("#viewInvoice");
    const $modalContent = $viewInvoiceModal.find(".modal-content");

    const originalTitle = document.title;

    let invoiceNum = "Invoice";
    const invoiceElement = $viewInvoiceModal.find("#view_invoice_number");

    if (invoiceElement.length) {
        const fullText = invoiceElement.text();
        invoiceNum = fullText.replace("Invoice #: ", "").trim();
    }
    const now = new Date();
    const dateStr =
        now.getFullYear() +
        "-" +
        (now.getMonth() + 1).toString().padStart(2, "0") +
        "-" +
        now.getDate().toString().padStart(2, "0");

    const filename = `${dateStr}-invoice-${invoiceNum}-Tinatangi-Cafe`;
    document.title = filename;

    const $printContent = $modalContent.clone();
    $viewInvoiceModal.css("display", "none").removeClass("show");

    $printContent.attr("id", "temp-print-content");
    $("body").append($printContent);

    const printStyles = `
        @media print {
            /* Hide everything on the page by default, then make only the print content visible */
            body > *:not(#temp-print-content) {
                display: none !important;
            }

            /* Global Body Reset for Print */
            body {
                margin: 0 !important;
                padding: 0 !important;
                font-size: 10pt;
                color: #000 !important;
            }

            /* --- CRITICAL FULL-PAGE EXPANSION --- */
            #temp-print-content {
                display: block !important;
                visibility: visible !important;

                /* Force full width and remove all margins/padding that might center it */
                width: 100% !important;
                max-width: 100vw !important;
                min-width: 100% !important;

                margin: 0 !important;
                padding: 0 !important;

                box-shadow: none !important;
                border: none !important;
                background-color: white !important;
                text-align: left !important;
            }

            /* Add some standard print padding (like standard printer margins) */
            #temp-print-content .modal-body {
                padding: 1.5rem 1rem !important;
            }

            /* Hide modal chrome (header buttons, close, footer) which are now part of the clone */
            #temp-print-content .modal-header,
            #temp-print-content .modal-footer,
            #temp-print-content [data-dismiss="modal"],
            #temp-print-content .close {
                display: none !important;
            }

            /* Adjust text colors */
            .text-primary, .text-secondary, .text-success {
                color: #000 !important;
            }

            /* --- HEADER ALIGNMENT FIX (Aggressive Float-based 2-Column Layout) --- */
            /* Ensure the main row is a clearfix container */
            #temp-print-content > .row.mb-4.p-3 {
                overflow: hidden !important; /* Contains floats */
                padding: 0 0 !important;
                margin: 0 !important;
            }

            /* Target ALL relevant Bootstrap column classes and force 50% width and float left */
            #temp-print-content .col-md-6,
            #temp-print-content .col-sm-6,
            #temp-print-content .col-6 {
                float: left !important;
                width: 50% !important;
                max-width: 50% !important;
                min-width: 50% !important;
            }

            /* Override the text-md-end class on the right column */
            #temp-print-content .col-md-6.text-md-end,
            #temp-print-content .col-md-6:nth-child(2) {
                float: right !important; /* Force it to the right */
                text-align: right !important;
            }

            /* Ensure the remarks section (col-md-12) takes full width and stacks below */
            #temp-print-content .col-md-12 {
                clear: both !important; /* Clear floats */
                width: 100% !important;
                max-width: 100% !important;
                margin-top: 1rem !important;
                text-align: left !important;
            }
            /* --- END HEADER ALIGNMENT FIX --- */


            /* --- TABLE STYLING FOR COMPACTNESS --- */
            /* Reduce vertical padding in ALL table cells to minimum */
            #temp-print-content table.table-sm th,
            #temp-print-content table.table-sm td {
                padding: 1px 0.25rem !important; /* Top/Bottom: 1px, Left/Right: 0.25rem */
                line-height: 1.25 !important; /* Adjust line height for dense content */
            }
            /* --- END TABLE STYLING --- */


            /* --- TABLE COLUMN WIDTHS --- */
            #temp-print-content table.table-sm {
                width: 100% !important;
                table-layout: fixed;
                margin-bottom: 2rem !important;
            }

            /* Column Width Distribution: 100% Total (Note: If columns are missing, adjust based on your final HTML) */
            .table-sm th:first-child, .table-sm td:first-child { width: 5% !important; text-align: center; } /* # */
            .table-sm th:nth-child(2), .table-sm td:nth-child(2) { width: 40% !important; } /* Item Name */
            .table-sm th:nth-child(3), .table-sm td:nth-child(3) { width: 10% !important; } /* Unit */
            .table-sm th:nth-child(4), .table-sm td:nth-child(4) { width: 20% !important; } /* Unit Price */
            .table-sm th:nth-child(5), .table-sm td:nth-child(5) { width: 15% !important; } /* Qty */
            .table-sm th:nth-child(6), .table-sm td:nth-child(6) { width: 15% !important; } /* Total Price */


            /* Add a title/header for the printed page */
            body::before {
                content: "INVOICE DETAILS";
                display: block;
                text-align: center;
                font-size: 14pt;
                margin-top: 10px;
                margin-bottom: 5px;
                font-weight: bold;
                visibility: visible !important;
            }

            /* Page Break handling */
            .table-responsive {
                overflow: visible !important;
            }
        }
    `;

    const $printStyleElement = $(
        '<style type="text/css" id="print-temp-style">'
    ).text(printStyles);
    $("head").append($printStyleElement);

    const cleanupAndRestore = () => {
        // Prevent running if cleanup already occurred
        // if ($printContent.parent().length === 0) return;

        window.removeEventListener("focus", cleanupAndRestore);

        $printStyleElement.remove();

        $printContent.remove();

        $viewInvoiceModal.css("display", "block");
        $viewInvoiceModal.modal("show");
        document.title = originalTitle;
    };

    window.addEventListener("focus", cleanupAndRestore);

    const mediaQueryList = window.matchMedia("print");
    mediaQueryList.addListener((mql) => {
        if (!mql.matches) {
            cleanupAndRestore();
        }
    });

    window.print();

    setTimeout(() => {
        cleanupAndRestore();
    }, 300);
}
