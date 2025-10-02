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
            url: "/procurement/purchases/get-list",
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
                data: "purchase_orders", // Use the array itself as data source
                title: "Order No.",
                className: "dt-left",
                render: function (data) {
                    // Check if data is an array and has at least one element
                    if (data && data.length > 0) {
                        // Access the specific named key from the first element
                        return data[0].purchase_order_id;
                    }
                    return "N/A";
                },
                className: "dt-left",
            },
            { data: "type", className: "dt-left" },
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
                title: "Supplier",
                className: "dt-left",
                render: function (data) {
                    if (data && data.length > 0) {
                        // Access the supplier_name key from the first element
                        return data[0].supplier_name;
                    }
                    return "N/A";
                },
                className: "dt-left",
            },
            // {
            //     data: "expected_delivery_date",
            //     render: function (data) {
            //         return data ? formatDate(data) : "N/A";
            //     },
            //     type: "date",
            //     className: "dt-left",
            // },
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
            // { data: "delivery_name", className: "dt-left", width: "150px" },
            { data: "requested_by_id", className: "dt-left" },
            { data: "remarks", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    // Use the business ID for viewing details (often for display/retrieval)
                    // const viewId = row.purchase_request_id;

                    // Use the internal primary key ID for transactional actions (Approve/Reject)
                    // const actionId = row.id;

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
                        '<span class="badge bg-warning">Approved<br>Pending Dispatch</span>'
                    ) {
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
                                    <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                        '<span class="badge bg-success">Accepted<br>Supplier</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}" 
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 accept-btn"
                                data-id="${data}" 
                                title="Mark as Delivered">
                                    <i class="fa-solid fa-truck"></i>
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
                width: "150px",
            },
        ],
    });

    $(document).on("click", ".process-btn", function () {
        const req_id = $(this).data("id");
        Swal.fire({
            title: "Process Purchase Order?",
            text: "You are about to request this order to supplier.",
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
                url: `/procurement/purchases/order/${req_id}/21`,
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
    $(document).on("click", ".accept-btn", function () {
        const req_id = $(this).data("id");
        Swal.fire({
            title: "Order Received?",
            text: "You are about to mark this order as delivered.",
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
                url: `/procurement/purchases/order/${req_id}/16`,
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

    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }

    $(document).on("click", ".btn-view", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/finance/purchases/get-details/${id}`, function (response) {
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
        
        <!-- SINGLE COMBINED ITEMS TABLE -->
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
});
