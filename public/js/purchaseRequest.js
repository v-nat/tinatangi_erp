$(document).ready(function () {
    $("#purchaseReqTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/finance/purchases/get-requests",
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
                width: "50px",
            },
            { data: "type" },
            { data: "department" },
            {
                data: "amount",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            { data: "requested_by_id" },
            { data: "requested_date" },
            { data: "remarks" },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (
                        row.status !==
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
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 process-btn"
                                data-id="${data}"
                                title="Process">
                                    <i class="fa-solid fa-check"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                            </a>
                        </div>
                        `;
                    }
                },
                className: "text-center",
            },
        ],
    });

    $(document).on("click", ".process-btn", function () {
        const req_id = $(this).data("id");

        Swal.fire({
            title: "Process Purchase Request?",
            text: "You are about to put on process this purchase order request.",
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
                url: `/finance/purchases/process/${req_id}/14`,
                type: "PUT",
                data: null,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("purchaseReqTable");
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

    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        $("#rejectionRequestId").val(id);
        $("#RejectionConfirmation").modal("show");
    });

    $("#reject-btn-confirmed").click(function (e) {
        e.preventDefault();
        let id = $("#rejectionRequestId").val();
        let remarks = $("#rejectionNotes").val();
        if (remarks) {
            $("#LoadingScreen").fadeIn(200);
            $("#rejectionModal").modal("hide");
            $.ajax({
                url: `/finance/purchases/process/${id}/12`,
                method: "PUT",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    id: id,
                    remarks: remarks,
                },
                success: function (response) {
                    if (response.success) {
                        $("#LoadingScreen").fadeOut(200);
                        reloadTable("purchaseReqTable");
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

    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
    
        // --- 1. AJAX Success Handler (Fixed) ---
    $(document).on("click", ".btn-view", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(
            `/finance/purchases/get-details/${id}`, 
            function (response) {
                if (response.data && response.data.length > 0) {
                    // Get the single Purchase Request object
                    const requestData = response.data[0]; 
                    buildPOmodal(requestData);
                } else {
                    // Handle case where no record was found for the ID
                    // Use a message box/custom modal instead of alert
                    alert("Error: Purchase Request not found.");
                }
            }
        ).fail(function (xhr) {
            const errorMsg = xhr.responseJSON ? xhr.responseJSON.error : "Failed to load purchase request details.";
            // Use a message box/custom modal instead of alert
            alert(errorMsg); 
        }).always(function() {
            $("#LoadingScreen").fadeOut(200); 
        });
    });

    // --- 2. Dynamic Modal Builder (Updated to include PO Header) ---
    function buildPOmodal(data) {
        // --- PREPARE GLOBAL PR HEADER INFO (Suppliers and PO summary) ---
        const uniqueSuppliers = new Set();
        const poSummaries = [];
        let allDetailRowsHtml = ""; // Accumulator for all item rows across all POs
        let itemIndex = 0; // Global index for the combined table

        if (data.purchase_orders && data.purchase_orders.length > 0) {
            data.purchase_orders.forEach((order) => {
                // Collect unique supplier names
                uniqueSuppliers.add(order.supplier_name || "N/A");

                const details = order.details || [];
                const supplierName = order.supplier_name || "N/A";


                // --- BUILD THE DETAIL TABLE ROWS (ACCUMULATE ALL ITEMS) ---
                if (details.length > 0) {
                    // Loop through each detail line item and add to the single list
                    details.forEach((item) => {
                        itemIndex++; // Increment global index
                        allDetailRowsHtml += `
                        <tr>
                            <td>${itemIndex}</td>
                            <td>${item.item_name || "N/A"}</td>
                            <td>${item.item_unit || "N/A"}</td>
                            <td class="text-end">₱${parseFloat(
                                item.unit_price || 0
                            ).toFixed(2)}</td>
                            <td class="text-end">${item.quantity || 0} ${item.item_unit || "N/A"}</td>
                            <td class="text-end">₱${parseFloat(
                                item.total_amount || 0
                            ).toFixed(2)}</td>
                        </tr>
                    `;
                    });
                }
                // Note: No 'else' here, as we only show a "No items" message if the whole combined table is empty.
            });
        }
        
        // Convert set to comma-separated list
        const supplierList = Array.from(uniqueSuppliers).join(', ');

        // Handle case where no items were found across ALL POs
        if (allDetailRowsHtml === "") {
            allDetailRowsHtml = `<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>`;
        }


        // --- 4. BUILD THE MASTER MODAL HTML (USING REQUEST DATA AND DYNAMIC CONTENT) ---
        const html = `
        <div class="row mb-4 p-3">
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
        
        <!-- SINGLE COMBINED ITEMS TABLE -->
        <div class="px-3">
            <h5 class="mb-3 text-primary">All Associated Line Items</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped">
                    <thead>
                        <tr class="table-secondary">
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


});
