import {
    buildInvoiceModal,
    buildPOmodal,
    printInvoice,
} from "./utils/modal_builders_scm.js";
import { reloadTable } from "./utils/reloadTable.js";
import { formatDate } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const purchaseOrderTable = $("#purchaseOrderTable").DataTable({
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
            { data: "type", className: "dt-left" },
            {
                data: "purchase_orders",
                title: "Order Date",
                className: "dt-left",
                render: {
                    _: function (data, type, row) {
                        let orderDate =
                            data && data.length > 0 ? data[0].order_date : null;
                        if (type === "display") {
                            return orderDate ? formatDate(orderDate) : "N/A";
                        }
                        // Return raw data for filtering
                        return orderDate;
                    },
                },
                className: "dt-left",
            },
            {
                data: "purchase_orders",
                title: "Supplier",
                className: "dt-left",
                render: function (data) {
                    if (data && data.length > 0) {
                        return data[0].supplier_name;
                    }
                    return "N/A";
                },
                className: "dt-left",
            },
            {
                data: "purchase_orders",
                title: "Delivery Date",
                render: {
                    _: function (data, type, row) {
                        let deliveryDate =
                            data && data.length > 0
                                ? data[0].delivery_date
                                : null;
                        if (type === "display") {
                            return deliveryDate
                                ? formatDate(deliveryDate)
                                : "N/A";
                        }
                        // Return raw data for filtering
                        return deliveryDate;
                    },
                },
                className: "dt-left",
            },
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
                                    <i class="fa-solid fa-paper-plane"></i>
                            </a>
                        </div>
                        `;
                    } else if (
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
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 accept-btn me-2"
                                data-id="${data}"
                                data-invoice-id="${invoice_id}"
                                title="Mark as Delivered">
                                    <i class="fa-solid fa-circle-check"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                            '<span class="badge bg-success">Delivered</span>' ||
                        row.status ==
                            '<span class="badge bg-success">Completed</span>'
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
                width: "150px",
            },
            {
                data: "invoice_id",
                visible: false,
            },
        ],
        initComplete: function () {
            const supplierColumn = this.api().column(4);
            const supplierSelect = $("#supplier_filter");

            const supplierNames = new Set();

            supplierColumn.data().each(function (d, j) {
                let supplierName = "N/A";
                if (d && d.length > 0) {
                    supplierName = d[0].supplier_name;
                }

                if (supplierName && supplierName !== "N/A") {
                    supplierNames.add(supplierName);
                }
            });

            const sortedNames = Array.from(supplierNames).sort();
            sortedNames.forEach(function (name) {
                supplierSelect.append(
                    $("<option></option>").attr("value", name).text(name)
                );
            });

            const statusColumn = this.api().column(8);
            const statusSelect = $("#status_filter");
            const statusValues = new Set();

            statusColumn
                .data()
                .unique()
                .each(function (d, j) {
                    if (d) {
                        let statusText = $(d).text();
                        if (!statusText) statusText = d;

                        statusValues.add(statusText);
                    }
                });
            const sortedStatuses = Array.from(statusValues).sort();

            sortedStatuses.forEach(function (text) {
                statusSelect.append(
                    $("<option></option>").attr("value", text).text(text)
                );
            });
        },
    });

    $("#order_date_filter").on("change", function () {
        purchaseOrderTable.column(3).search($(this).val(), false, true).draw();
    });

    $("#supplier_filter").on("change", function () {
        const selectedSupplier = $(this).val();
        purchaseOrderTable
            .column(4)
            .search(
                selectedSupplier ? "^" + selectedSupplier + "$" : "",
                true,
                false
            )
            .draw();
    });

    $("#delivery_date_filter").on("change", function () {
        purchaseOrderTable.column(5).search($(this).val(), false, true).draw();
    });

    $("#status_filter").on("change", function () {
        const selectedStatus = $(this).val();
        purchaseOrderTable
            .column(8)
            .search(selectedStatus, false, false)
            .draw();
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
        const invoice_id = $(this).data("invoice-id");
        console.log(invoice_id);
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
                data: {
                    invoice_id: invoice_id,
                },
                processData: true, // Should be true for form-encoded data, false for FormData (binary)
                contentType: "application/x-www-form-urlencoded; charset=UTF-8", // Default for jQuery AJAX PUT/POST

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

        $.get(`/procurement/purchases/get-details/${id}`, function (response) {
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

        $.get(`/procurement/purchases/get-invoice/${id}`, function (response) {
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
        printInvoice();
    });
});
