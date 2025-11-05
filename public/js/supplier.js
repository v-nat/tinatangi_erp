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
                            data-bs-toggle="tooltip"
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
                            data-bs-toggle="tooltip"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 approve-btn"
                                data-id="${data}"
                                data-bs-toggle="tooltip"
                                title="Approve & Ship">
                                    <i class="fa-solid fa-truck"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                data-bs-toggle="tooltip"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                            '<span class="badge bg-danger">Return</span>' ||
                        row.status ==
                            '<span class="badge bg-warning">Partial Delivered</span>'
                    ) {
                        return `
                        <div class="action-btns">
                             <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            data-bs-toggle="tooltip"
                            title="View Original Order">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger btn-view-returns bs-tooltip me-2"
                            data-id="${data}"
                            data-bs-toggle="tooltip"
                            title="Process Returns">
                                <i class="fa-solid fa-person-walking-arrow-right"></i>
                            </a>
                        </div>
                        `;
                    } else if (
                        row.status ==
                            '<span class="badge bg-success">Completed</span>' ||
                        row.status ==
                            '<span class="badge bg-success">Delivered</span>' ||
                        row.status ==
                            '<span class="badge bg-success">Accepted<br>Supplier</span>' ||
                        row.status ==
                            '<span class="badge bg-danger">Cancelled<br>Supplier</span>' ||
                        row.status ==
                            '<span class="badge bg-success">Redeliver<br>Supplier</span>' ||
                        row.status ==
                            '<span class="badge bg-warning">Approved<br>Pending Dispatch</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-invoice bs-tooltip me-2"
                            data-id="${invoice_id}"
                            data-bs-toggle="tooltip"
                            title="View Invoice">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                        </div>
                        `;
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            data-bs-toggle="tooltip"
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
        drawCallback: function () {
            var tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },
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
        printInvoice();
    });

    $(document).on("click", ".btn-view-returns", function () {
        const req_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $("#processReturnForm")[0].reset();
        $("#returnItemsList").html(
            '<tr><td colspan="4" class="text-center">Loading items...</td></tr>'
        );

        $.get(`/supplier/returns/get-details/${req_id}`, function (response) {
            const items = response.data;
            const $itemList = $("#returnItemsList");
            $itemList.empty();
            $("#return_pr_id").val(req_id);

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

                const rowHtml = `
                    <tr class="item-row" data-index="${index}">
                        <input type="hidden" name="items[${index}][pod_id]" value="${item.pod_id}">

                        <td class="align-top">
                            <strong>${item.item_name}</strong>
                            <br>
                            <small class="text-muted">Qty Returned: ${item.quantity}</small>
                        </td>
                        <td class="align-top">
                            ${item.reason}
                        </td>
                        <td class="align-top">
                            ${photoHtml}
                        </td>
                        <td class="align-middle">
                            <div class="form-check">
                                <input class="form-check-input supplier-return-action" type="radio" name="items[${index}][action]" id="action_redeliver_${index}" value="redeliver" data-index="${index}" checked>
                                <label class="form-check-label" for="action_redeliver_${index}">
                                    Redeliver
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input supplier-return-action" type="radio" name="items[${index}][action]" id="action_cancel_${index}" value="cancel" data-index="${index}">
                                <label class="form-check-label" for="action_cancel_${index}">
                                    Cancel Item
                                </label>
                            </div>

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
                xhr.responseJSON?.error ||
                    "Failed to load returned item details.",
                "error"
            );
        });
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
                        xhr.responseJSON?.error ||
                            "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    });
});
