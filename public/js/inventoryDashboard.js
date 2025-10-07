$(document).ready(function () {
    // Define the limit for how many alerts to show initially
    const ALERT_LIMIT = 3;

    // Target container where the alerts will be displayed
    const ALERT_CONTAINER_ID = "#invClaims";

    // Variable to store the full list of requests so it can be accessed
    // by the 'View All' click handler later.
    let allPurchaseRequests = [];

    /**
     * Fetches all Purchase Requests with status 16 (Ready to Receive).
     */
    function getToReceiveRequests() {
        fetch(`/inventory/get-to-receive`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        "Network response was not ok: " + response.statusText
                    );
                }
                return response.json();
            })
            .then((response) => {
                // Store the full list of requests globally
                allPurchaseRequests = response.data || [];
                $(ALERT_CONTAINER_ID).empty();
                // Pass the initial limit to show only 5 requests
                buildReceiveAlerts(allPurchaseRequests, ALERT_LIMIT);
            })
            .catch((error) => {
                console.error("Error fetching 'To Receive' data:", error);
                $(ALERT_CONTAINER_ID).html(
                    `<div class="alert alert-danger">Error loading pending receipts.</div>`
                );
            });
    }

    /**
     * Builds and inserts HTML alerts for pending Purchase Requests.
     * @param {Array<Object>} requests - Array of Purchase Request objects.
     * @param {number} [limit=requests.length] - The maximum number of requests to display.
     */
    function buildReceiveAlerts(requests, limit = requests.length) {
        // Clear the container before rendering
        $(ALERT_CONTAINER_ID).empty();

        if (!Array.isArray(requests) || requests.length === 0) {
            $(ALERT_CONTAINER_ID).html(
                `<div class="alert alert-light-success">No purchase requests are currently ready for receiving.</div>`
            );
            return;
        }

        const requestsToDisplay = requests.slice(0, limit);
        const hasMore = requests.length > limit;

        requestsToDisplay.forEach((request) => {
            const invoiceId = request.invoice_id;
            const requestId = request.id;
            const supplierName = request.supplier_name || "N/A";

            let totalItemsQuantity = 0;

            if (
                request.purchase_orders &&
                Array.isArray(request.purchase_orders)
            ) {
                request.purchase_orders.forEach((po) => {
                    if (po.details && Array.isArray(po.details)) {
                        po.details.forEach((detail) => {
                            totalItemsQuantity += detail.quantity || 0;
                        });
                    }
                });
            }

            const alertHtml = `
                <div class="alert alert-light-success alert-dismissible fade show" role="alert">
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8 col-md-8 justify-content-start d-flex">
                            <div class="d-block">
                                <h6>PR NO: ${requestId}</h6>
                                <p class="mb-1 ">From: ${supplierName}</p>
                                <p class="mb-0 ">Total Item(s): ${totalItemsQuantity}</p>
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 col-md-4 p-0 justify-content-end align-items-center d-flex">
                            <a href="#" class="btn icon btn-sm btn-info btn-invoice bs-tooltip me-2" data-id="${invoiceId}" title="View Request">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-success btn-receive-request bs-tooltip me-2" data-id="${requestId}" title="Receive Inventory">
                                <i class="fa-solid fa-box-open"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;

            $(ALERT_CONTAINER_ID).append(alertHtml);
        });

        // Add the "View All" link if more items are available
        if (hasMore) {
            const remainingCount = requests.length - limit;
            const viewAllHtml = `
                <div class="text-center mt-3" id="viewAllContainer">
                    <a href="#" class="btn btn-sm btn-outline-info btn-show-all">
                        View All ${remainingCount} More Requests
                    </a>
                </div>
            `;
            $(ALERT_CONTAINER_ID).append(viewAllHtml);
        }
    }

    // Use event delegation on the document for dynamically added elements
    $(document).on("click", ".btn-show-all", function (e) {
        e.preventDefault();
        if (allPurchaseRequests.length > 0) {
            buildReceiveAlerts(allPurchaseRequests, allPurchaseRequests.length);
        } else {
            console.error("Full request list is not available.");
        }
    });

    getToReceiveRequests();

    $(document).on("click", ".btn-invoice", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/inventory/item-to-receive/get-invoice/${id}`, function (response) {
            if (response.data) {
                const requestData = response.data;
                buildInvoiceModal(requestData);
            } else {
                alert("Error: Invoice not found.");
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load purchase invoice details.";
                alert(errorMsg);
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    });

    $(document).on("click", "#print", function () {
        const $viewInvoiceModal = $("#viewInvoice");
        const $modalContent = $viewInvoiceModal.find(".modal-content");
        const $printContent = $modalContent.clone();

        // 1. Temporarily hide the original modal window and its backdrop
        $viewInvoiceModal.css("display", "none").removeClass("show");

        // 2. Insert the cloned content directly into the body for printing
        // This allows the content to escape the width constraints of the Bootstrap modal.
        $printContent.attr("id", "temp-print-content");
        $("body").append($printContent);

        // 3. Define the print-specific CSS rules as a single string
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

        // 4. Inject the temporary CSS into the document head
        const $printStyleElement = $(
            '<style type="text/css" id="print-temp-style">'
        ).text(printStyles);
        $("head").append($printStyleElement);

        // 5. Cleanup function
        const cleanupAndRestore = () => {
            // Prevent running if cleanup already occurred
            // if ($printContent.parent().length === 0) return;

            // Remove event listeners first to prevent repeat execution
            window.removeEventListener("focus", cleanupAndRestore);
            // Note: mql.removeListener(cleanupAndRestore) is the correct way for media queries,
            // but we'll rely on the focus listener and the parent check for simplicity/compatibility.

            // Remove the temporary style block
            $printStyleElement.remove();

            // Remove the temporary content from the body
            $printContent.remove();

            // Restore the modal: Rely solely on Bootstrap's function to handle class/display state
            $viewInvoiceModal.css("display", "block");
            $viewInvoiceModal.modal("show");
        };

        // 6. Monitor for print completion/cancellation
        // The print dialog pauses execution. When it closes, the window regains focus.
        window.addEventListener("focus", cleanupAndRestore);

        // Alternative: Use media query listener for better compatibility (also handles cancellation)
        const mediaQueryList = window.matchMedia("print");
        mediaQueryList.addListener((mql) => {
            if (!mql.matches) {
                // This runs after printing is complete or cancelled
                cleanupAndRestore();
            }
        });

        // 7. Trigger the print dialog
        window.print();

        // Fallback cleanup (in case event listeners fail or are unsupported)
        setTimeout(() => {
            cleanupAndRestore();
        }, 300);
    });

    function buildInvoiceModal(data) {
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
            <p class="mb-1">Requested by: ${employeeName || "N/A"}</p>
            <p class="mb-0">Supplier: ${data.supplier_name}</p>
            <p class="mb-0">Delivery #: ${data.delivery_no || "N/A"}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <p class="mb-1">Invoice #: ${data.id || "N/A"}</p>
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
});
