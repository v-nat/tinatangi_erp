import {
    buildInvoiceModal,
    buildPOmodal,
    printInvoice,
} from "./utils/modal_builders_scm.js";
import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }

    let currentOrderId = null;
    let currentInvoiceId = null;

    const STATUS_PENDING_SUPPLIER =
        '<span class="badge bg-warning">Pending<br>Supplier</span>';
    const STATUSES_RETURN = [
        '<span class="badge bg-danger">Return</span>',
        '<span class="badge bg-warning">Partial Delivered</span>',
    ];
    const STATUSES_HAS_INVOICE = [
        '<span class="badge bg-success">Completed</span>',
        '<span class="badge bg-success">Delivered</span>',
        '<span class="badge bg-success">Accepted<br>Supplier</span>',
        '<span class="badge bg-danger">Cancelled<br>Supplier</span>',
        '<span class="badge bg-success">Redeliver<br>Supplier</span>',
        '<span class="badge bg-warning">Approved<br>Pending Dispatch</span>',
    ];

    const supplierOrderTable = $("#purchaseOrderTable").DataTable({
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
            },
            {
                data: "purchase_orders",
                title: "Delivery Date",
                className: "dt-left",
                render: function (data) {
                    if (data && data.length > 0) {
                        const deliveryDate = data[0].delivery_date;
                        return deliveryDate ? formatDate(deliveryDate) : "N/A";
                    }
                    return "N/A";
                },
                type: "date",
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
                    const invoiceId = row.invoice_id || "";
                    let statusKey = "";
                    if (row.status === STATUS_PENDING_SUPPLIER) {
                        statusKey = "pending-supplier";
                    } else if (STATUSES_RETURN.includes(row.status)) {
                        statusKey = "return";
                    } else if (STATUSES_HAS_INVOICE.includes(row.status)) {
                        statusKey = "has-invoice";
                    }
                    return `
                    <div class="action-btns">
                        <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip"
                        data-id="${data}"
                        data-invoice-id="${invoiceId}"
                        data-status-key="${statusKey}"
                        data-bs-toggle="tooltip"
                        title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                    `;
                },
                className: "text-center",
                width: "170px",
            },
            {
                data: "invoice_id",
                visible: false,
            },
        ],
        drawCallback: function () {
            var tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },
    });

    $(document).on("click", ".btn-view", function () {
        const id = $(this).data("id");
        const invoiceId = $(this).data("invoice-id");
        const statusKey = $(this).data("status-key");

        currentOrderId = id;
        currentInvoiceId = invoiceId;

        // Show appropriate modal footer buttons based on status
        $("#supplier-approve-btn, #supplier-reject-btn, #supplier-returns-btn, #supplier-invoice-btn").addClass("d-none");
        if (statusKey === "pending-supplier") {
            $("#supplier-approve-btn").removeClass("d-none");
            $("#supplier-reject-btn").removeClass("d-none");
        } else if (statusKey === "return") {
            $("#supplier-returns-btn").removeClass("d-none");
        } else if (statusKey === "has-invoice") {
            $("#supplier-invoice-btn").removeClass("d-none");
        }

        $("#LoadingScreen").fadeIn(200);

        $.get(`/supplier/purchases/get-details/${id}`, function (response) {
            if (response.data && response.data.length > 0) {
                buildPOmodal(response.data[0]);
            } else {
                Toast.fire("Error", "Purchase Request not found.", "error");
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load purchase request details.";
                Toast.fire("Error", errorMsg, "error");
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    });

    // Approve & Ship from inside the modal
    $("#supplier-approve-btn").on("click", function () {
        $("#viewPO").modal("hide");
        $("#approveOrderId").val(currentOrderId);
        $("#approveItemsList").html(
            '<tr><td colspan="4" class="text-center text-muted">Loading items...</td></tr>'
        );
        $("#approveOrderModal").modal("show");

        $.get(`/supplier/purchases/get-details/${currentOrderId}`, function (response) {
            if (!response.data || response.data.length === 0) {
                $("#approveItemsList").html(
                    '<tr><td colspan="4" class="text-center text-danger">No items found.</td></tr>'
                );
                return;
            }
            const requestData = response.data[0];
            let rowsHtml = "";
            let itemIndex = 0;
            (requestData.purchase_orders || []).forEach((order) => {
                (order.details || []).forEach((item) => {
                    itemIndex++;
                    const expInput = item.is_perishable
                        ? `<input type="date" class="form-control form-control-sm approve-exp-date-input" data-pod-id="${item.pod_id}" style="min-width:130px;">`
                        : `<span class="text-muted small">Not perishable</span>`;
                    rowsHtml += `
                        <tr>
                            <td>${itemIndex}</td>
                            <td>${item.item_name || "N/A"}</td>
                            <td class="text-end">${item.quantity || 0} ${item.item_unit || ""}</td>
                            <td>${expInput}</td>
                        </tr>`;
                });
            });
            if (!rowsHtml) {
                rowsHtml = '<tr><td colspan="4" class="text-center text-muted">No items found.</td></tr>';
            }
            $("#approveItemsList").html(rowsHtml);
        }).fail(function () {
            $("#approveItemsList").html(
                '<tr><td colspan="4" class="text-center text-danger">Failed to load items.</td></tr>'
            );
        });
    });

    // Reject from inside the modal — open rejection modal
    $("#supplier-reject-btn").on("click", function () {
        $("#rejectionReqId").val(currentOrderId);
        $("#rejectionNotes").val("");
        $("#viewPO").modal("hide");
        $("#RejectionConfirmation").modal("show");
    });

    // View Invoice from inside the modal
    $("#supplier-invoice-btn").on("click", function () {
        $("#viewPO").modal("hide");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/supplier/purchases/get-invoice/${currentInvoiceId}`, function (response) {
            if (response.data) {
                buildInvoiceModal(response.data);
            } else {
                Toast.fire("Error", "Invoice not found.", "error");
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load purchase invoice details.";
                Toast.fire("Error", errorMsg, "error");
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    });

    // Process Returns from inside the modal
    $("#supplier-returns-btn").on("click", function () {
        const orderId = currentOrderId;
        $("#viewPO").modal("hide");
        $("#LoadingScreen").fadeIn(200);

        $("#processReturnForm")[0].reset();
        $("#returnItemsList").html(
            '<tr><td colspan="4" class="text-center">Loading items...</td></tr>'
        );

        $.get(`/supplier/returns/get-details/${orderId}`, function (response) {
            const items = response.data;
            const $itemList = $("#returnItemsList");
            $itemList.empty();
            $("#return_pr_id").val(orderId);

            if (!items || items.length === 0) {
                $itemList.html(
                    '<tr><td colspan="4" class="text-center text-danger">No returned items found for this order.</td></tr>'
                );
                $("#LoadingScreen").fadeOut(200);
                $("#processReturnModal").modal("show");
                return;
            }

            items.forEach((item, index) => {
                const photoHtml = item.photo_path
                    ? `<a href="${item.photo_path}" target="_blank">
                           <img src="${item.photo_path}" alt="Return Photo" style="max-width: 150px; max-height: 100px; border-radius: 5px;">
                       </a>`
                    : `<span class="text-muted">No photo</span>`;

                const qtyContext = item.delivered_qnty > 0
                    ? `<small class="text-muted d-block">Ordered: <strong>${item.quantity}</strong> &nbsp;|&nbsp; Already received: <strong class="text-success">${item.delivered_qnty}</strong> &nbsp;|&nbsp; Returned: <strong class="text-danger">${item.backorder_qnty}</strong></small>`
                    : `<small class="text-muted d-block">Ordered: <strong>${item.quantity}</strong> &nbsp;|&nbsp; Returned: <strong class="text-danger">${item.backorder_qnty}</strong></small>`;

                const cancelNote = item.delivered_qnty > 0
                    ? `<small class="text-warning d-block mt-1"><i class="fa-solid fa-triangle-exclamation"></i> Cancelling will keep the already-received ${item.delivered_qnty} unit(s) and remove only the returned portion.</small>`
                    : '';

                const rowHtml = `
                    <tr class="item-row" data-index="${index}">
                        <input type="hidden" name="items[${index}][pod_id]" value="${item.pod_id}">

                        <td class="align-top">
                            <strong>${item.item_name}</strong>
                            ${qtyContext}
                        </td>
                        <td class="align-top">
                            ${item.reason}
                        </td>
                        <td class="align-top">
                            ${photoHtml}
                        </td>
                        <td class="align-middle">
                            <div class="form-check">
                                <input class="form-check-input supplier-return-action" type="radio"
                                    name="items[${index}][action]" id="action_redeliver_${index}"
                                    value="redeliver" data-index="${index}" checked>
                                <label class="form-check-label" for="action_redeliver_${index}">
                                    Redeliver (${item.backorder_qnty} unit(s))
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input supplier-return-action" type="radio"
                                    name="items[${index}][action]" id="action_cancel_${index}"
                                    value="cancel" data-index="${index}">
                                <label class="form-check-label" for="action_cancel_${index}">
                                    Cancel Return
                                </label>
                            </div>
                            ${cancelNote}

                            <div class="cancel-reason-field" id="cancel_reason_field_${index}" style="display: none;">
                                <textarea class="form-control form-control-sm mt-2"
                                          name="items[${index}][cancel_reason]"
                                          placeholder="Reason for cancellation..."
                                          disabled></textarea>
                            </div>
                        </td>
                    </tr>
                `;
                $itemList.append(rowHtml);
            });

            $("#LoadingScreen").fadeOut(200);
            $("#processReturnModal").modal("show");
        }).fail(function (xhr) {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire(
                "Error",
                xhr.responseJSON?.error || "Failed to load returned item details.",
                "error"
            );
        });
    });

    // Confirm rejection from the rejection modal
    $("#reject-btn-confirmed").click(function (e) {
        e.preventDefault();
        let req_id = $("#rejectionReqId").val();
        let reason = $("#rejectionNotes").val();

        if (reason) {
            $("#LoadingScreen").fadeIn(200);
            $("#RejectionConfirmation").modal("hide");
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

    // Confirm approval from the approve modal
    $(document).on("click", "#confirmApproveBtn", function () {
        const req_id = $("#approveOrderId").val();
        const expirationDates = {};
        $(".approve-exp-date-input").each(function () {
            const podId = $(this).data("pod-id");
            const dateVal = $(this).val();
            if (podId && dateVal) {
                expirationDates[podId] = dateVal;
            }
        });
        $("#approveOrderModal").modal("hide");
        $("#LoadingScreen").fadeIn(200);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: `/supplier/orders/process/${req_id}/20`,
            type: "PUT",
            data: { expiration_dates: expirationDates },
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
                    let errorMessages = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("\n");
                    Toast.fire("Validation Error", errorMessages, "error");
                } else {
                    Toast.fire("Error", "An unexpected error occurred.", "error");
                }
            },
        });
    });

    // Reset modal state when closed
    $("#viewPO").on("hidden.bs.modal", function () {
        currentOrderId = null;
        currentInvoiceId = null;
        $("#supplier-approve-btn, #supplier-reject-btn, #supplier-returns-btn, #supplier-invoice-btn").addClass("d-none");
    });

    $(document).on("change", ".supplier-return-action", function () {
        const index = $(this).data("index");
        const $reasonFieldDiv = $("#cancel_reason_field_" + index);
        const $reasonTextarea = $reasonFieldDiv.find("textarea");

        if ($(this).val() === "cancel") {
            $reasonFieldDiv.show();
            $reasonTextarea.prop("disabled", false).prop("required", true);
        } else {
            $reasonFieldDiv.hide();
            $reasonTextarea.prop("disabled", true).prop("required", false);
        }
    });

    $("#processReturnForm").on("submit", function (e) {
        e.preventDefault();
        $("#LoadingScreen").fadeIn(200);

        let hasCancelWithoutReason = false;
        $("#returnItemsList .item-row").each(function (index) {
            const action = $(this).find(`.supplier-return-action:checked`).val();
            const $reasonTextarea = $(this).find(`textarea[name="items[${index}][cancel_reason]"]`);

            if (action === 'cancel' && !$reasonTextarea.val()) {
                hasCancelWithoutReason = true;
                $reasonTextarea.addClass('is-invalid');
            } else {
                $reasonTextarea.removeClass('is-invalid');
            }
        });

        if (hasCancelWithoutReason) {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire(
                "Error",
                "Please provide a reason for all cancelled items.",
                "error"
            );
            return;
        }

        const formData = $(this).serialize();

        $.ajax({
            url: "/supplier/returns/process",
            type: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                $("#processReturnModal").modal("hide");
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
                        xhr.responseJSON?.error || "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    });

    $(document).on("click", "#print", function () {
        printInvoice();
    });

    $("#btn-refresh-supplier-orders").on("click", function () {
        supplierOrderTable.ajax.reload(null, false);
    });
});
