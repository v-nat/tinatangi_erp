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
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 reject-btn"
                                data-id="${data}" 
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
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

    $(document).on("click", ".reject-btn", function () {
        const req_id = $(this).data("id");
        Swal.fire({
            title: "Decline Purchase Order?",
            text: "You are about reject this order from Tinatangi Cafe.",
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
                url: `/supplier/orders/process/${req_id}/19`,
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
});
