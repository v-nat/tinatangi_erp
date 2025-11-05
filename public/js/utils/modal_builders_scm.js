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

    // --- ADDED THIS BLOCK ---
    let proofPhotoHtml = "";
    if (data.overall_photo_path) {
        proofPhotoHtml = `
        <hr>
        <div class="row mt-3 px-3">
            <div class="col-12">
                <h6 class="mb-2">Delivery Proof</h6>
                <a href="${data.overall_photo_path}" target="_blank">
                    <img src="${data.overall_photo_path}" class="img-fluid img-thumbnail" alt="Delivery Proof" style="max-height: 350px; width: auto;">
                </a>
            </div>
        </div>
        `;
    }
    // -----------------------

    const html = `
    <div class="row mb-4 p-3">
        <div class="col-md-6">
            <p class="mb-0">Requested by: ${employeeName || "N/A"}</p>
            <p class="mb-0">Supplier: ${data.supplier_name}</p>
            <p class="mb-0">Delivery #: ${data.delivery_no || "N/A"}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <p id="view_invoice_number" class="mb-0">Invoice #: ${
                data.id || "N/A"
            }</p>
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

    ${proofPhotoHtml}

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

// export function printInvoice() {
//     const $viewInvoiceModal = $("#viewInvoice");
//     const $modalContent = $viewInvoiceModal.find(".modal-content");

//     const originalTitle = document.title;

//     let invoiceNum = "Invoice";
//     const invoiceElement = $viewInvoiceModal.find("#view_invoice_number");

//     if (invoiceElement.length) {
//         const fullText = invoiceElement.text();
//         invoiceNum = fullText.replace("Invoice #: ", "").trim();
//     }
//     const now = new Date();
//     const dateStr =
//         now.getFullYear() +
//         "-" +
//         (now.getMonth() + 1).toString().padStart(2, "0") +
//         "-" +
//         now.getDate().toString().padStart(2, "0");

//     const filename = `${dateStr}-invoice-${invoiceNum}-Tinatangi-Cafe`;
//     document.title = filename;

//     const $printContent = $modalContent.clone();
//     $viewInvoiceModal.css("display", "none").removeClass("show");

//     $printContent.attr("id", "temp-print-content");
//     $("body").append($printContent);

//     const printStyles = `
//         @media print {
//             /* Hide everything on the page by default, then make only the print content visible */
//             body > *:not(#temp-print-content) {
//                 display: none !important;
//             }

//             /* Global Body Reset for Print */
//             body {
//                 margin: 0 !important;
//                 padding: 0 !important;
//                 font-size: 10pt;
//                 color: #000 !important;
//             }

//             /* --- CRITICAL FULL-PAGE EXPANSION --- */
//             #temp-print-content {
//                 display: block !important;
//                 visibility: visible !important;

//                 /* Force full width and remove all margins/padding that might center it */
//                 width: 100% !important;
//                 max-width: 100vw !important;
//                 min-width: 100% !important;

//                 margin: 0 !important;
//                 padding: 0 !important;

//                 box-shadow: none !important;
//                 border: none !important;
//                 background-color: white !important;
//                 text-align: left !important;
//             }

//             /* Add some standard print padding (like standard printer margins) */
//             #temp-print-content .modal-body {
//                 padding: 1.5rem 1rem !important;
//             }

//             /* Hide modal chrome (header buttons, close, footer) which are now part of the clone */
//             #temp-print-content .modal-header,
//             #temp-print-content .modal-footer,
//             #temp-print-content [data-dismiss="modal"],
//             #temp-print-content .close {
//                 display: none !important;
//             }

//             /* Adjust text colors */
//             .text-primary, .text-secondary, .text-success {
//                 color: #000 !important;
//             }

//             /* --- HEADER ALIGNMENT FIX (Aggressive Float-based 2-Column Layout) --- */
//             /* Ensure the main row is a clearfix container */
//             #temp-print-content > .row.mb-4.p-3 {
//                 overflow: hidden !important; /* Contains floats */
//                 padding: 0 0 !important;
//                 margin: 0 !important;
//             }

//             /* Target ALL relevant Bootstrap column classes and force 50% width and float left */
//             #temp-print-content .col-md-6,
//             #temp-print-content .col-sm-6,
//             #temp-print-content .col-6 {
//                 float: left !important;
//                 width: 50% !important;
//                 max-width: 50% !important;
//                 min-width: 50% !important;
//             }

//             /* Override the text-md-end class on the right column */
//             #temp-print-content .col-md-6.text-md-end,
//             #temp-print-content .col-md-6:nth-child(2) {
//                 float: right !important; /* Force it to the right */
//                 text-align: right !important;
//             }

//             /* Ensure the remarks section (col-md-12) takes full width and stacks below */
//             #temp-print-content .col-md-12 {
//                 clear: both !important; /* Clear floats */
//                 width: 100% !important;
//                 max-width: 100% !important;
//                 margin-top: 1rem !important;
//                 text-align: left !important;
//             }
//             /* --- END HEADER ALIGNMENT FIX --- */


//             /* --- TABLE STYLING FOR COMPACTNESS --- */
//             /* Reduce vertical padding in ALL table cells to minimum */
//             #temp-print-content table.table-sm th,
//             #temp-print-content table.table-sm td {
//                 padding: 1px 0.25rem !important; /* Top/Bottom: 1px, Left/Right: 0.25rem */
//                 line-height: 1.25 !important; /* Adjust line height for dense content */
//             }
//             /* --- END TABLE STYLING --- */


//             /* --- TABLE COLUMN WIDTHS --- */
//             #temp-print-content table.table-sm {
//                 width: 100% !important;
//                 table-layout: fixed;
//                 margin-bottom: 2rem !important;
//             }

//             /* Column Width Distribution: 100% Total (Note: If columns are missing, adjust based on your final HTML) */
//             .table-sm th:first-child, .table-sm td:first-child { width: 5% !important; text-align: center; } /* # */
//             .table-sm th:nth-child(2), .table-sm td:nth-child(2) { width: 40% !important; } /* Item Name */
//             .table-sm th:nth-child(3), .table-sm td:nth-child(3) { width: 10% !important; } /* Unit */
//             .table-sm th:nth-child(4), .table-sm td:nth-child(4) { width: 20% !important; } /* Unit Price */
//             .table-sm th:nth-child(5), .table-sm td:nth-child(5) { width: 15% !important; } /* Qty */
//             .table-sm th:nth-child(6), .table-sm td:nth-child(6) { width: 15% !important; } /* Total Price */


//             /* Add a title/header for the printed page */
//             body::before {
//                 content: "INVOICE DETAILS";
//                 display: block;
//                 text-align: center;
//                 font-size: 14pt;
//                 margin-top: 10px;
//                 margin-bottom: 5px;
//                 font-weight: bold;
//                 visibility: visible !important;
//             }

//             /* Page Break handling */
//             .table-responsive {
//                 overflow: visible !important;
//             }
//         }
//     `;

//     const $printStyleElement = $(
//         '<style type="text/css" id="print-temp-style">'
//     ).text(printStyles);
//     $("head").append($printStyleElement);

//     const cleanupAndRestore = () => {
//         // Prevent running if cleanup already occurred
//         // if ($printContent.parent().length === 0) return;

//         window.removeEventListener("focus", cleanupAndRestore);

//         $printStyleElement.remove();

//         $printContent.remove();

//         $viewInvoiceModal.css("display", "block");
//         $viewInvoiceModal.modal("show");
//         document.title = originalTitle;
//     };

//     window.addEventListener("focus", cleanupAndRestore);

//     const mediaQueryList = window.matchMedia("print");
//     mediaQueryList.addListener((mql) => {
//         if (!mql.matches) {
//             cleanupAndRestore();
//         }
//     });

//     window.print();

//     setTimeout(() => {
//         cleanupAndRestore();
//     }, 300);
// }

export function printInvoice() {
    const $viewInvoiceModal = $("#viewInvoice");
    const originalTitle = document.title;

    // --- 1. DATA EXTRACTION ---
    // Extract dynamic data that is still needed
    let invoiceNum = "N/A";
    const invoiceElement = $viewInvoiceModal.find("#view_invoice_number");
    if (invoiceElement.length) {
        invoiceNum = invoiceElement.text().replace("Invoice #: ", "").trim();
    }

    const dateApproved = $viewInvoiceModal.find('p:contains("Date Approved:")').text().replace("Date Approved: ", "").trim() || new Date().toLocaleDateString();
    const deliveryNum = $viewInvoiceModal.find('p:contains("Delivery #:")').text().replace("Delivery #: ", "").trim() || "N/A";
    const approvedBy = $viewInvoiceModal.find('p:contains("Approved By:")').text().replace("Approved By: ", "").trim() || "N/A";
    const requestedBy = $viewInvoiceModal.find('p:contains("Requested by:")').text().replace("Requested by: ", "").trim() || "N/A";

    const totalAmount = $viewInvoiceModal.find('td:contains("Total Amount:")').next().text().trim() || "₱0.00";

    // Extract table items
    const items = [];
    $viewInvoiceModal.find("table tbody tr").each(function () {
        const $row = $(this);
        // Skip the final "Total Amount" row
        if ($row.find('td:contains("Total Amount:")').length > 0) {
            return;
        }

        items.push({
            name: $row.find("td:nth-child(2)").text().trim(),
            unit: $row.find("td:nth-child(3)").text().trim(),
            unitPrice: $row.find("td:nth-child(4)").text().trim(),
            quantity: $row.find("td:nth-child(5)").text().trim().split(" ")[0], // Get just the number
            total: $row.find("td:nth-child(6)").text().trim(),
        });
    });

    // Extract delivery photo
    const $deliveryPhoto = $viewInvoiceModal.find('img[alt="Delivery Proof"]');
    const deliveryPhotoSrc = $deliveryPhoto.length ? $deliveryPhoto.attr("src") : null;

    // Set document title for printing
    const now = new Date();
    const dateStr = now.getFullYear() + "-" + (now.getMonth() + 1).toString().padStart(2, "0") + "-" + now.getDate().toString().padStart(2, "0");
    const filename = `${dateStr}-invoice-${invoiceNum}-Tinatangi-Cafe`;
    document.title = filename;

    // --- 2. HTML STRING BUILDING ---
    // Build the item rows (NO EMPTY ROWS)
    let itemRowsHtml = "";
    items.forEach(item => {
        itemRowsHtml += `
            <tr>
                <td class="text-center">${item.quantity}</td>
                <td>${item.unit}</td>
                <td>${item.name}</td>
                <td class="text-end">${item.unitPrice}</td>
                <td class="text-end">${item.total}</td>
            </tr>
        `;
    });

    // --- Page 1: Invoice ---
    const page1Html = `
        <div class="print-invoice-page">
            <header class="print-header">
                <div class="header-left">
                    <h1 class="company-name">Tinatangi Cafe</h1>
                    <p>Brgy 13 Jose Abad Santos Ave, Dasmariñas, 4114 Cavite</p>
                </div>
                <div class="header-right">
                    <h1 class="invoice-title">SALES INVOICE</h1>
                    <div class="invoice-num-box">
                        <span class="invoice-num-label">Nº</span>
                        <span class="invoice-num">${invoiceNum}</span>
                    </div>
                </div>
            </header>

            <section class="print-customer-details">
                <div class="sold-to">
                    <p><strong>SOLD TO:</strong> Tinatangi Cafe</p>
                    <p><strong>ADDRESS:</strong> Brgy 13 Jose Abad Santos Ave, Dasmariñas, 4114 Cavite</p>
                    <p><strong>BUSINESS STYLE:</strong> coffee shop</p>
                </div>
                <div class="invoice-meta">
                    <p><strong>DATE:</strong> ${dateApproved}</p>
                    <p><strong>P.O. #:</strong> ${deliveryNum}</p>
                </div>
            </section>

            <section class="print-item-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 8%;">QTY.</th>
                            <th style="width: 12%;">UNIT</th>
                            <th style="width: 45%;">PARTICULARS</th>
                            <th style="width: 15%;" class="text-end">UNIT PRICE</th>
                            <th style="width: 20%;" class="text-end">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemRowsHtml}
                    </tbody>
                </table>
            </section>

            <footer class="print-footer">
                <div class="summary-left">
                    <div class="summary-box">
                        <div class="summary-row total">
                            <span><strong>TOTAL AMOUNT</strong></span>
                            <span><strong>${totalAmount}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="signatures-right">
                    <div class="sig-box">
                        <strong>Prepared by:</strong>
                        <p class="sig-line"></p>
                        <p class="sig-name">${requestedBy}</p>
                    </div>
                    <div class="sig-box">
                        <strong>Approved by:</strong>
                        <p class="sig-line"></p>
                        <p class="sig-name">${approvedBy}</p>
                    </div>
                    <div class="received-box">
                        <p>Received goods in good order and condition</p>
                        <p class="sig-line"></p>
                        <p class="sig-name">Please Print Name & Sign</p>
                    </div>
                </div>
            </footer>
        </div>
    `;

    // --- Page 2: Delivery Proof ---
    let page2Html = "";
    if (deliveryPhotoSrc) {
        page2Html = `
            <div class="delivery-proof-page">
                <h2 class="proof-title">Delivery Proof</h2>
                <img src="${deliveryPhotoSrc}" alt="Delivery Proof">
            </div>
        `;
    }

    // Combine HTML and append to body
    const finalPrintHtml = page1Html + page2Html;
    $("body").append(`<div id="temp-print-content">${finalPrintHtml}</div>`);

    // --- 3. CSS STYLING ---
    const printStyles = `
        @media print {
            /* Hide everything else */
            body > *:not(#temp-print-content) {
                display: none !important;
            }

            /* Basic page setup */
            @page {
                size: A4 portrait;
                margin: 0.5in; /* Reduced margins */
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                color: #000 !important;
                font-family: 'Arial', sans-serif;
                font-size: 8.5pt; /* Reduced base font size */
            }

            /* Make print content visible and full-width */
            #temp-print-content {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .text-end { text-align: right; }
            .text-center { text-align: center; }
            p { margin: 1px 0; } /* Tightened margins */

            /* --- Page 1: Invoice --- */
            .print-invoice-page {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            /* Header */
            .print-header {
                display: flex;
                justify-content: space-between;
                border-bottom: 2px solid #000;
                padding-bottom: 5px;
            }
            .header-left .company-name {
                font-size: 12pt; /* Reduced */
                font-weight: bold;
                margin: 0;
            }
            .header-right {
                text-align: right;
            }
            .header-right .invoice-title {
                font-size: 14pt; /* Reduced */
                font-weight: bold;
                margin: 0;
                color: #333;
            }
            .header-right .invoice-num-box {
                border: 1px solid #000;
                padding: 2px 5px;
                display: inline-block;
                margin-top: 5px;
            }
            .invoice-num-label {
                font-weight: bold;
                margin-right: 10px;
            }
            .invoice-num {
                font-weight: bold;
                font-size: 11pt; /* Reduced */
            }

            /* Customer Details */
            .print-customer-details {
                display: flex;
                margin-top: 10px; /* Reduced */
                padding-bottom: 10px; /* Reduced */
                border-bottom: 1px solid #000;
            }
            .sold-to { flex: 3; }
            .invoice-meta { flex: 2; }

            /* Item Table */
            .print-item-table {
                margin-top: 5px; /* Reduced */
                flex-grow: 1; /* This makes the table fill the space */
            }
            .print-item-table table {
                width: 100%;
                border-collapse: collapse;
            }
            .print-item-table th,
            .print-item-table td {
                border: 1px solid #000;
                padding: 3px 5px; /* Reduced */
                vertical-align: top;
            }
            .print-item-table th {
                background-color: #eee;
                text-align: center;
                font-size: 8pt;
            }
            /* Style for the *last* row of items */
            .print-item-table tbody tr:last-child td {
                 border-bottom: 1px solid #000;
            }


            /* Footer */
            .print-footer {
                display: flex;
                justify-content: space-between;
                margin-top: 10px; /* Reduced */
                padding-top: 10px;
                border-top: 2px solid #000;
                width: 100%;
            }

            /* UPDATED SUMMARY */
            .summary-left {
                flex: 1.2;
                display: flex;
                align-items: center; /* Vertically center the box */
            }
            .summary-box {
                border: 1px solid #000;
                width: 90%;
            }
            .summary-row.total {
                display: flex;
                justify-content: space-between;
                padding: 8px 10px;
                font-size: 10pt;
                font-weight: bold;
                background-color: #eee;
            }
            /* END UPDATED SUMMARY */

            .signatures-right {
                flex: 1.5;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .sig-box {
                margin-bottom: 10px; /* Reduced */
            }
            .sig-line {
                border-bottom: 1px solid #000;
                margin-top: 15px; /* Reduced */
                margin-bottom: 2px;
            }
            .sig-name {
                font-size: 8pt;
                text-align: center;
            }
            .received-box {
                font-weight: bold;
            }

            /* --- Page 2: Delivery Proof --- */
            .delivery-proof-page {
                page-break-before: always; /* THIS IS THE KEY */
                padding-top: 0.5in; /* Reduced */
                text-align: center;
            }
            .delivery-proof-page .proof-title {
                font-size: 14pt; /* Reduced */
                font-weight: bold;
                margin-bottom: 15px;
            }
            .delivery-proof-page img {
                max-width: 90%;
                border: 1px solid #999;
                padding: 5px;
            }
        }
    `;

    // --- 4. PRINT & CLEANUP ---
    const $printStyleElement = $(
        '<style type="text/css" id="print-temp-style">'
    ).text(printStyles);
    $("head").append($printStyleElement);

    const cleanupAndRestore = () => {
        $printStyleElement.remove();
        $("#temp-print-content").remove();
        document.title = originalTitle;

        window.removeEventListener("focus", cleanupAndRestore);
        if (mediaQueryList) {
            mediaQueryList.removeListener(mqlListener);
        }
    };

    let cleanupCalled = false;
    const doCleanup = () => {
        if (!cleanupCalled) {
            cleanupCalled = true;
            cleanupAndRestore();
        }
    };

    window.addEventListener("focus", doCleanup);

    const mqlListener = (mql) => {
        if (!mql.matches) {
            doCleanup();
        }
    };
    const mediaQueryList = window.matchMedia("print");
    mediaQueryList.addListener(mqlListener);

    window.print();

    setTimeout(doCleanup, 1500);
}
