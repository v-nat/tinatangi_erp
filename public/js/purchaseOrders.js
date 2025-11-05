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
                                title="Receive Delivery">
                                    <i class="fa-solid fa-truck-fast"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                        '<span class="badge bg-success">Redeliver<br>Supplier</span>'
                    ) {
                        // --- NEW BUTTON FOR REDELIVERY ---
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View Original Order">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-success bs-tooltip me-2 btn-receive-redelivery me-2"
                                data-id="${data}"
                                title="Receive Redelivery">
                                    <i class="fa-solid fa-boxes-packing"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                            '<span class="badge bg-success">Delivered</span>' ||
                        row.status ==
                            '<span class="badge bg-success">Completed</span>' ||
                        row.status ==
                            '<span class="badge bg-warning">Partial Delivered</span>' ||
                        row.status ==
                            '<span class="badge bg-danger">Return</span>'
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

    // =================================================================
    // CLICK HANDLER: ".accept-btn" (Receive *Initial* Delivery)
    // =================================================================
    $(document).on("click", ".accept-btn", function () {
        const req_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        // Reset form
        $("#receiveOrderForm")[0].reset();
        $("#receiveItemsList").html(
            '<tr><td colspan="4" class="text-center">Loading items...</td></tr>'
        );

        // Fetch item details for the modal
        $.get(`/procurement/purchases/get-delivery-details/${req_id}`, function (
            response
        ) {
            const items = response.data;
            const $itemList = $("#receiveItemsList");
            $itemList.empty();
            $("#receive_pr_id").val(req_id);

            if (!items || items.length === 0) {
                $itemList.html(
                    '<tr><td colspan="4" class="text-center text-danger">No items found for this order.</td></tr>'
                );
                $("#LoadingScreen").fadeOut(200);
                $("#receiveOrderModal").modal("show");
                return;
            }

            items.forEach((item, index) => {
                const rowHtml = `
                    <tr class="item-row" data-index="${index}">
                        <td class="text-center align-middle">
                            <!-- This is the visible checkbox -->
                            <input type="checkbox" class="form-check-input item-received-check" data-index="${index}" checked>

                            <!-- These are the hidden fields that will be submitted -->
                            <input type="hidden" class="item-status-hidden" name="items[${index}][status]" value="received">
                            <input type="hidden" name="items[${index}][pod_id]" value="${item.pod_id}">
                        </td>
                        <td class="align-middle">
                            <strong>${item.item_name}</strong>
                        </td>
                        <td class="align-middle">
                            ${item.quantity_ordered} ${item.item_unit}
                        </td>
                        <td class="align-middle">
                            <div class="return-fields" id="return_fields_${index}" style="display: none;">
                                <textarea class="form-control mb-2" name="items[${index}][return_reason]" placeholder="Reason for return..." disabled></textarea>
                                <input type="file" class="form-control" name="items[${index}][return_photo]" accept="image/*" disabled>
                            </div>
                        </td>
                    </tr>
                `;
                $itemList.append(rowHtml);
            });

            $("#LoadingScreen").fadeOut(200);
            $("#receiveOrderModal").modal("show");
        }).fail(function (xhr) {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire(
                "Error",
                xhr.responseJSON?.error ||
                    "Failed to load item details for inspection.",
                "error"
            );
        });
    });

    // =================================================================
    // CLICK HANDLER: ".btn-receive-redelivery" (Receive *Redelivered* Items)
    // =================================================================
    $(document).on("click", ".btn-receive-redelivery", function () {
        const req_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        // Reset form
        $("#receiveRedeliveryForm")[0].reset();
        $("#redeliveryItemsList").html(
            '<tr><td colspan="4" class="text-center">Loading items...</td></tr>'
        );

        // Fetch item details for the modal
        $.get(
            `/procurement/purchases/get-redelivery-details/${req_id}`,
            function (response) {
                const items = response.data;
                const $itemList = $("#redeliveryItemsList");
                $itemList.empty();
                $("#receive_redelivery_pr_id").val(req_id);

                if (!items || items.length === 0) {
                    $itemList.html(
                        '<tr><td colspan="4" class="text-center text-danger">No items found for redelivery.</td></tr>'
                    );
                    $("#LoadingScreen").fadeOut(200);
                    $("#receiveRedeliveryModal").modal("show");
                    return;
                }

                items.forEach((item, index) => {
                    const rowHtml = `
                    <tr class="item-row-redelivery" data-index="${index}">
                        <td class="text-center align-middle">
                            <input type="checkbox" class="form-check-input item-redelivery-check" data-index="${index}" checked>
                            <input type="hidden" class="item-status-redelivery-hidden" name="items[${index}][status]" value="received">
                            <input type="hidden" name="items[${index}][pod_id]" value="${item.pod_id}">
                        </td>
                        <td class="align-middle">
                            <strong>${item.item_name}</strong>
                        </td>
                        <td class="align-middle">
                            ${item.quantity_ordered} ${item.item_unit}
                        </td>
                        <td class="align-middle">
                            <div class="return-fields-redelivery" id="return_fields_redelivery_${index}" style="display: none;">
                                <textarea class="form-control mb-2" name="items[${index}][return_reason]" placeholder="Reason for returning again..." disabled></textarea>
                                <input type="file" class="form-control" name="items[${index}][return_photo]" accept="image/*" disabled>
                            </div>
                        </td>
                    </tr>
                `;
                    $itemList.append(rowHtml);
                });

                $("#LoadingScreen").fadeOut(200);
                $("#receiveRedeliveryModal").modal("show");
            }
        ).fail(function (xhr) {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire(
                "Error",
                xhr.responseJSON?.error ||
                    "Failed to load redelivery item details.",
                "error"
            );
        });
    });

    // =================================================================
    // CHANGE HANDLER: Checkbox for *Initial* Delivery
    // =================================================================
    $(document).on("change", ".item-received-check", function () {
        const index = $(this).data("index");
        const $returnFields = $("#return_fields_" + index);
        const $hiddenStatus = $(this)
            .closest("tr")
            .find(".item-status-hidden");
        const $fieldsToToggle = $returnFields.find(
            "textarea, input[type=file]"
        );

        if ($(this).is(":checked")) {
            // Item is received
            $returnFields.hide();
            $fieldsToToggle.prop("disabled", true).prop("required", false); // Disable and remove required
            $hiddenStatus.val("received");
            $(this).closest("tr").removeClass("table-danger");
        } else {
            // Item is returned
            $returnFields.show();
            $fieldsToToggle.prop("disabled", false);
            $fieldsToToggle.filter("textarea").prop("required", true); // Make reason required
            $hiddenStatus.val("returned");
            $(this).closest("tr").addClass("table-danger");
        }
    });

    // =================================================================
    // CHANGE HANDLER: Checkbox for *Redelivery*
    // =================================================================
    $(document).on("change", ".item-redelivery-check", function () {
        const index = $(this).data("index");
        const $returnFields = $("#return_fields_redelivery_" + index);
        const $hiddenStatus = $(this)
            .closest("tr")
            .find(".item-status-redelivery-hidden");
        const $fieldsToToggle = $returnFields.find(
            "textarea, input[type=file]"
        );

        if ($(this).is(":checked")) {
            // Item is received
            $returnFields.hide();
            $fieldsToToggle.prop("disabled", true).prop("required", false);
            $hiddenStatus.val("received");
            $(this).closest("tr").removeClass("table-danger");
        } else {
            // Item is returned *again*
            $returnFields.show();
            $fieldsToToggle.prop("disabled", false);
            $fieldsToToggle.filter("textarea").prop("required", true);
// Make reason required
            $hiddenStatus.val("returned_again");
            $(this).closest("tr").addClass("table-danger");
        }
    });

    // =================================================================
    // SUBMIT HANDLER: *Initial* Delivery Form
    // =================================================================
    $("#receiveOrderForm").on("submit", function (e) {
        e.preventDefault();
        $("#LoadingScreen").fadeIn(200);

        const formData = new FormData(this);

        // Client-side validation
        let hasUncheckedWithoutReason = false;
        $("#receiveItemsList .item-row").each(function () {
            const isChecked = $(this).find(".item-received-check").is(":checked");
            const reason = $(this).find("textarea").val();
            if (!isChecked && !reason) {
                hasUncheckedWithoutReason = true;
                $(this).find("textarea").addClass("is-invalid");
            } else {
                $(this).find("textarea").removeClass("is-invalid");
            }
        });

        if (hasUncheckedWithoutReason) {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire(
                "Error",
                "Please provide a reason for all returned items.",
                "error"
            );
            return;
        }

        $.ajax({
            url: "/procurement/purchases/receive-delivery",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                $("#receiveOrderModal").modal("hide");
                reloadTable("purchaseOrderTable");
                Toast.fire({
                    text: response.message,
                    icon: "success",
                });
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                if (xhr.status === 422) {
                    // Validation errors
                    let errorMessages =
                        "<strong>Validation Failed:</strong><ul class='text-start'>";
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        errorMessages += `<li>${value[0]}</li>`;
                    });
                    errorMessages += "</ul>";
                    Toast.fire({
                        title: "Error",
                        html: errorMessages,
                        icon: "error",
                    });
                } else {
                    Toast.fire(
                        "Error",
                        xhr.responseJSON?.error ||
                            "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    });

    // =================================================================
    // SUBMIT HANDLER: *Redelivery* Form
    // =================================================================
    $("#receiveRedeliveryForm").on("submit", function (e) {
        e.preventDefault();
        $("#LoadingScreen").fadeIn(200);

        const formData = new FormData(this);

        // Client-side validation
        let hasUncheckedWithoutReason = false;
        $("#redeliveryItemsList .item-row-redelivery").each(function () {
            const isChecked = $(this).find(".item-redelivery-check").is(":checked");
            const reason = $(this).find("textarea").val();
            if (!isChecked && !reason) {
                hasUncheckedWithoutReason = true;
                $(this).find("textarea").addClass("is-invalid");
            } else {
                $(this).find("textarea").removeClass("is-invalid");
            }
        });

        if (hasUncheckedWithoutReason) {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire(
                "Error",
                "Please provide a reason for all items being returned again.",
                "error"
            );
            return;
        }

        $.ajax({
            url: "/procurement/purchases/receive-redelivery", // New endpoint
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                $("#receiveRedeliveryModal").modal("hide");
                reloadTable("purchaseOrderTable");
                Toast.fire({
                    text: response.message,
                    icon: "success",
                });
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                if (xhr.status === 422) {
                    let errorMessages =
                        "<strong>Validation Failed:</strong><ul class='text-start'>";
                    $.each(xhr.responseJSON.errors, function (key, value) {
                        errorMessages += `<li>${value[0]}</li>`;
                    });
                    errorMessages += "</ul>";
                    Toast.fire({
                        title: "Error",
                        html: errorMessages,
                        icon: "error",
                    });
                } else {
                    Toast.fire(
                        "Error",
                        xhr.responseJSON?.error ||
                            "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    });
});
