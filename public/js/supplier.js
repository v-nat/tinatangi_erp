$(document).ready(function () {
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }
    $("#purchaseOrderTable").DataTable({
        responsive: true,
        scrollX: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/supplier/orders/get-list",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
                width: "45px",
            },
            {
                data: "purchase_orders",
                title: "Order No.",
                className: "dt-left",
                render: function (data) {
                    if (data && data.length > 0) {
                        return data[0].purchase_order_id;
                    }
                    return "N/A";
                },
                className: "dt-left",
            },
            {
                data: "purchase_orders",
                title: "Order Date",
                className: "dt-left",
                render: function (data) {
                    if (data && data.length > 0) {
                        const orderDate = data[0].order_date;
                        return orderDate ? formatDate(orderDate) : "N/A";
                    }
                    return "N/A";
                },
                type: "date",
                className: "dt-left",
            },
            {
                data: "purchase_orders",
                title: "Delivery Date",
                render: function (data) {
                    if (data && data.length > 0) {
                        const deliveryDate = data[0].delivery_date;
                        return deliveryDate ? formatDate(deliveryDate) : "N/A";
                    }
                    return "N/A";
                },
                type: "date",
                className: "dt-left",
            },
            { data: "remarks", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    let invoice_id = null;
                    if (row.invoice_id) {
                        invoice_id = row.invoice_id;
                    }
                    if (
                        row.status ==
                        '<span class="badge bg-warning">Pending</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                        '<span class="badge bg-warning">Pending<br>Supplier</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 approve-btn"
                                data-id="${data}"
                                title="Approve">
                                    <i class="fa-solid fa-truck"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                        '<span class="badge bg-success">Delivered</span>' ||
                        row.status ==
                        '<span class="badge bg-success">Accepted<br>Supplier</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-invoice bs-tooltip me-2"
                            data-id="${invoice_id}"
                            title="Invoice">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                        `;
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                        `;
                    }
                },
                className: "text-center",
                width: "170px",
            },

            {
                data: "invoice_id",
                visible: false,
            },
        ],
    });

    $(document).on("click", ".approve-btn", function () {
        const req_id = $(this).data("id");
        Swal.fire({
            title: "Approve Purchase Order?",
            text: "You are about to ship this order to Tinatangi Cafe.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm!",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: `/supplier/orders/process/${req_id}/20`,
                type: "PUT",
                data: null,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("purchaseOrderTable");
                    Toast.fire({
                        text: response.message,
                        icon: "success",
                    });
                },
                error: function (xhr) {
                    $("#LoadingScreen").fadeOut(200);
                    if (xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(
                            xhr.responseJSON.errors
                        )
                            .flat()
                            .join("\n");
                        Toast.fire("Validation Error", errorMessages, "error");
                    } else {
                        Toast.fire(
                            "Error",
                            "An unexpected error occurred.",
                            "error"
                        );
                    }
                },
            });
        });
    });

    $(document).on("click", ".btn-view", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/supplier/purchases/get-details/${id}`, function (response) {
            if (response.data && response.data.length > 0) {
                const requestData = response.data[0];
                buildPOmodal(requestData);
            } else {
                alert("Error: Purchase Request not found.");
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load purchase request details.";
                alert(errorMsg);
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    });
    $(document).on("click", ".btn-invoice", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/supplier/purchases/get-invoice/${id}`, function (response) {
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

    $(document).on("click", ".reject-btn", function () {
        const req_id = $(this).data("id");
        $("#rejectionReqId").val(req_id);
        $("#RejectionConfirmation").modal("show");
    });

    $("#reject-btn-confirmed").click(function (e) {
        e.preventDefault();
        let req_id = $("#rejectionReqId").val();
        let reason = $("#rejectionNotes").val();

        if (reason) {
            $("#LoadingScreen").fadeIn(200);
            $("#rejectionModal").modal("hide");
            $.ajax({
                url: `/supplier/orders/process/${req_id}/19`,
                method: "PUT",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    remarks: reason,
                },
                success: function (response) {
                    if (response.success) {
                        $("#LoadingScreen").fadeOut(200);
                        reloadTable("purchaseOrderTable");
                        Toast.fire("Rejected!", response.message, "success");
                    } else {
                        Toast.fire("Error", response.message, "error");
                    }
                },
                error: function (xhr) {
                    Toast.fire(
                        "Error",
                        xhr.responseJSON?.message || "Something went wrong",
                        "error"
                    );
                },
            });
        } else {
            Toast.fire({
                icon: "error",
                title: "Error",
                text: "Please provide a remarks",
                timer: 1500,
            });
        }
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
        const uniqueSuppliers = new Set();
        const employee = new Set();
        let allDetailRowsHtml = "";
        let itemIndex = 0;

        if (data.purchase_orders && data.purchase_orders.length > 0) {
            data.purchase_orders.forEach((order) => {
                uniqueSuppliers.add(order.supplier_name || "N/A");
                employee.add(order.created_by_id || "N/A");

                const details = order.details || [];

                if (details.length > 0) {
                    details.forEach((item) => {
                        itemIndex++;
                        allDetailRowsHtml += `
                    <tr>
                        <td>${itemIndex}</td>
                        <td>${item.item_name || "N/A"}</td>
                        <td>${item.item_unit || "N/A"}</td>
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

        const supplierList = Array.from(uniqueSuppliers).join(", ");
        const employeeName = Array.from(employee).join(", ");

        if (allDetailRowsHtml === "") {
            allDetailRowsHtml = `<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>`;
        }

        const html = `
    <div class="row mb-4 p-3">
        <!-- Invoice Header -->
        <div class="col-md-6">
            <p class="mb-1">Requested By: ${employeeName || "N/A"}</p>
            <p class="mb-0">Supplier: ${supplierList}</p>
            <p class="mb-0">Delivery #: ${data.delivery_no || "N/A"}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <p class="mb-1">Invoice ID: ${data.id || "N/A"}</p>
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

    function buildPOmodal(data) {
        const uniqueSuppliers = new Set();
        let allDetailRowsHtml = "";
        let itemIndex = 0;

        if (data.purchase_orders && data.purchase_orders.length > 0) {
            data.purchase_orders.forEach((order) => {
                uniqueSuppliers.add(order.supplier_name || "N/A");

                const details = order.details || [];

                if (details.length > 0) {
                    details.forEach((item) => {
                        itemIndex++;
                        allDetailRowsHtml += `
                        <tr>
                            <td>${itemIndex}</td>
                            <td>${item.item_name || "N/A"}</td>
                            <td>${item.item_unit || "N/A"}</td>
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

        const supplierList = Array.from(uniqueSuppliers).join(", ");

        if (allDetailRowsHtml === "") {
            allDetailRowsHtml = `<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>`;
        }

        const html = `
        <div class="row mb-4 p-3">
            ${data.status}
            <!-- Purchase Request Header -->
            <div class="col-md-6">
                <h6 class="mb-1">Requested By: <strong>${
                    data.requested_by_id || "N/A"
                }</strong></h6>
                <p class="mb-0">Department: ${data.department || "N/A"}</p>
                <p class="mb-0">Suppliers: <strong class="text-success">${supplierList}</strong></p> <!-- SUPPLIER MOVED HERE -->
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="mb-1">Purchase Request ID: <strong>${
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

    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
});
