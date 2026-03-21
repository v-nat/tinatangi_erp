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
                    const isReturned      = Boolean(item.is_returned);
                    const isPartialReturn = Boolean(item.is_partial_return);

                    let rowClass    = "";
                    let statusBadge = "";
                    if (isReturned) {
                        rowClass    = ' class="table-danger"';
                        statusBadge = '<span class="badge bg-danger ms-2">Returned</span>';
                    } else if (isPartialReturn) {
                        rowClass    = ' class="table-warning"';
                        statusBadge = '<span class="badge bg-warning text-dark ms-2">Partial Return</span>';
                    }

                    const qtyDisplay = isPartialReturn
                        ? `${item.delivered_qty}/${item.ordered_qty} ${item.item_unit || ""}`
                        : `${item.quantity || 0} ${item.item_unit || "N/A"}`;

                    allDetailRowsHtml += `
                        <tr${rowClass}>
                            <td>${itemIndex}</td>
                            <td>${item.item_name || "N/A"} ${statusBadge}</td>
                            <td>${item.item_unit_name || "N/A"}</td>
                            <td class="text-end">₱${parseFloat(
                                item.unit_price || 0
                            ).toFixed(2)}</td>
                            <td class="text-end">${qtyDisplay}</td>
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
                    const isReturned      = Boolean(item.is_returned);
                    const isPartialReturn = Boolean(item.is_partial_return);

                    let rowClass    = "";
                    let statusBadge = "";
                    if (isReturned) {
                        rowClass    = ' class="table-danger"';
                        statusBadge = '<span class="badge bg-danger ms-2">Returned</span>';
                    } else if (isPartialReturn) {
                        rowClass    = ' class="table-warning"';
                        statusBadge = '<span class="badge bg-warning text-dark ms-2">Partial Return</span>';
                    }

                    const qtyDisplay = isPartialReturn
                        ? `${item.delivered_qty}/${item.ordered_qty} ${item.item_unit || ""}`
                        : `${item.quantity || 0} ${item.item_unit || "N/A"}`;

                    allDetailRowsHtml += `
                        <tr${rowClass}>
                            <td>${itemIndex}</td>
                            <td>${item.item_name || "N/A"} ${statusBadge}</td>
                            <td>${item.item_unit_name || "N/A"}</td>
                            <td class="text-end">₱${parseFloat(
                                item.unit_price || 0
                            ).toFixed(2)}</td>
                            <td class="text-end">${qtyDisplay}</td>
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
            <div class="col-md-6">
                <h6 class="mb-0">Requested By: <strong>${
                    data.requested_by_id || "N/A"
                }</strong></h6>
                <p class="mb-0">Department: ${data.department || "N/A"}</p>
                <p class="mb-0">Supplier: <strong class="text-success">${
                    data.supplier_name
                }</strong></p>
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
    const originalTitle = document.title;

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

    const items = [];
    $viewInvoiceModal.find("table tbody tr").each(function () {
        const $row = $(this);
        if ($row.find('td:contains("Total Amount:")').length > 0) {
            return;
        }

        items.push({
            name: $row.find("td:nth-child(2)").text().trim(),
            unit: $row.find("td:nth-child(3)").text().trim(),
            unitPrice: $row.find("td:nth-child(4)").text().trim(),
            quantity: $row.find("td:nth-child(5)").text().trim().split(" ")[0],
            total: $row.find("td:nth-child(6)").text().trim(),
        });
    });

    const $deliveryPhoto = $viewInvoiceModal.find('img[alt="Delivery Proof"]');
    const deliveryPhotoSrc = $deliveryPhoto.length ? $deliveryPhoto.attr("src") : null;

    const now = new Date();
    const dateStr = now.getFullYear() + "-" + (now.getMonth() + 1).toString().padStart(2, "0") + "-" + now.getDate().toString().padStart(2, "0");
    const filename = `${dateStr}-invoice-${invoiceNum}-Tinatangi-Cafe`;
    document.title = filename;

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
                    <p><strong>BUSINESS STYLE:</strong> Restaurant Coffee Shop</p>
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

    let page2Html = "";
    if (deliveryPhotoSrc) {
        page2Html = `
            <div class="delivery-proof-page">
                <h2 class="proof-title">Delivery Proof</h2>
                <img src="${deliveryPhotoSrc}" alt="Delivery Proof">
            </div>
        `;
    }

    const finalPrintHtml = page1Html + page2Html;
    $("body").append(`<div id="temp-print-content">${finalPrintHtml}</div>`);

    const printStyles = `
        @media print {
            body > *:not(#temp-print-content) {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 0.5in;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                color: #000 !important;
                font-family: 'Arial', sans-serif;
                font-size: 8.5pt;
            }

            #temp-print-content {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .text-end { text-align: right; }
            .text-center { text-align: center; }
            p { margin: 1px 0; }

            .print-invoice-page {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .print-header {
                display: flex;
                justify-content: space-between;
                border-bottom: 2px solid #000;
                padding-bottom: 5px;
            }
            .header-left .company-name {
                font-size: 12pt;
                font-weight: bold;
                margin: 0;
            }
            .header-right {
                text-align: right;
            }
            .header-right .invoice-title {
                font-size: 14pt;
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
                font-size: 11pt;
            }

            .print-customer-details {
                display: flex;
                margin-top: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #000;
            }
            .sold-to { flex: 3; }
            .invoice-meta { flex: 2; }

            .print-item-table {
                margin-top: 5px;
                flex-grow: 1; /* This makes the table fill the space */
            }
            .print-item-table table {
                width: 100%;
                border-collapse: collapse;
            }
            .print-item-table th,
            .print-item-table td {
                border: 1px solid #000;
                padding: 3px 5px;
                vertical-align: top;
            }
            .print-item-table th {
                background-color: #eee;
                text-align: center;
                font-size: 8pt;
            }
            .print-item-table tbody tr:last-child td {
                 border-bottom: 1px solid #000;
            }

            .print-footer {
                display: flex;
                justify-content: space-between;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 2px solid #000;
                width: 100%;
            }

            .summary-left {
                flex: 1.2;
                display: flex;
                align-items: center;
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

            .signatures-right {
                flex: 1.5;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .sig-box {
                margin-bottom: 10px;
            }
            .sig-line {
                border-bottom: 1px solid #000;
                margin-top: 15px;
                margin-bottom: 2px;
            }
            .sig-name {
                font-size: 8pt;
                text-align: center;
            }
            .received-box {
                font-weight: bold;
            }

            .delivery-proof-page {
                page-break-before: always;
                padding-top: 0.5in;
                text-align: center;
            }
            .delivery-proof-page .proof-title {
                font-size: 14pt;
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
