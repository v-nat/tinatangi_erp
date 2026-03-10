import {
    buildInvoiceModal,
    buildPOmodal,
    printInvoice,
} from "./utils/modal_builders_scm.js";
import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    let currentRequestId = null;

    const COMPLETED_STATUSES = [
        '<span class="badge bg-success">Completed</span>',
        '<span class="badge bg-success">Delivered</span>',
        '<span class="badge bg-success">Accepted<br>Supplier</span>',
    ];
    const PENDING_STATUS = '<span class="badge bg-warning">Pending</span>';

    const purchaseReqTable = $("#purchaseReqTable").DataTable({
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
                width: "45px",
            },
            { data: "type", className: "dt-left" },
            { data: "department", className: "dt-left" },
            {
                data: "amount",
                className: "dt-left",
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
            { data: "requested_by_id", className: "dt-left" },
            { data: "requested_date", className: "dt-left" },
            { data: "remarks", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (COMPLETED_STATUSES.includes(row.status)) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-invoice bs-tooltip"
                            data-id="${row.invoice_id}"
                            title="Invoice">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                        `;
                    }
                    const isPending = row.status === PENDING_STATUS ? "1" : "0";
                    return `
                    <div class="action-btns">
                        <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip"
                        data-id="${data}"
                        data-is-pending="${isPending}"
                        title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                    `;
                },
                className: "text-center",
                width: "200px",
            },
            {
                data: "invoice_id",
                visible: false,
            },
        ],
    });

    $("#refresh-purchase-requests").on("click", function () {
        purchaseReqTable.ajax.reload();
    });

    $(document).on("click", ".btn-view", function () {
        const id = $(this).data("id");
        const isPending = $(this).data("is-pending") == "1";
        currentRequestId = id;
        $("#LoadingScreen").fadeIn(200);

        $.get(`/finance/purchases/get-details/${id}`, function (response) {
            if (response.data && response.data.length > 0) {
                buildPOmodal(response.data[0]);

                if (isPending) {
                    $("#po-modal-approve-btn").removeClass("d-none");
                    $("#po-modal-reject-btn").removeClass("d-none");
                } else {
                    $("#po-modal-approve-btn").addClass("d-none");
                    $("#po-modal-reject-btn").addClass("d-none");
                }
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

    // Approve from inside the modal
    $("#po-modal-approve-btn").on("click", function () {
        Swal.fire({
            title: "Process Purchase Request?",
            text: "You are about to put on process this purchase order request.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm!",
        }).then((result) => {
            if (!result.isConfirmed) return;

            $("#viewPO").modal("hide");
            $("#LoadingScreen").fadeIn(200);

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                url: `/finance/purchases/process/${currentRequestId}/14`,
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
    });

    // Reject button in view modal — open the rejection modal
    $("#po-modal-reject-btn").on("click", function () {
        $("#rejectionRequestId").val(currentRequestId);
        $("#rejectionNotes").val("");
        $("#viewPO").modal("hide");
        $("#RejectionConfirmation").modal("show");
    });

    // Confirm rejection from the rejection modal
    $("#reject-btn-confirmed").on("click", function () {
        const id = $("#rejectionRequestId").val();
        const remarks = $("#rejectionNotes").val().trim();

        if (!remarks) {
            Toast.fire("Error", "Please provide a rejection reason.", "error");
            return;
        }

        $("#RejectionConfirmation").modal("hide");
        $("#LoadingScreen").fadeIn(200);

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
    });

    // Reset modal state when closed
    $("#viewPO").on("hidden.bs.modal", function () {
        currentRequestId = null;
        $("#po-modal-approve-btn").addClass("d-none");
        $("#po-modal-reject-btn").addClass("d-none");
    });

    $(document).on("click", ".btn-invoice", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/finance/purchases/get-invoice/${id}`, function (response) {
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

    $(document).on("click", "#print", function () {
        printInvoice();
    });
});
