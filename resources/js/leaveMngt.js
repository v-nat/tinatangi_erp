import $ from 'jquery';
import Swal from 'sweetalert2';
import { reloadTable } from "./utils/reloadTable.js";
import { formatDate } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const dt = $("#leaves_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/human-resources/leaves/get",
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
            { data: "employee", className: "dt-left" },
            { data: "department", className: "dt-left" },
            { data: "position", className: "dt-left" },
            {
                data: "start_date",
                className: "dt-left",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date",
            },
            {
                data: "end_date",
                className: "dt-left",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date",
            },
            // {
            //     data: "reason",
            //     className: "dt-left",
            // },
            // {
            //     data: "attachment_url",
            //     className: "text-center",
            //     orderable: false,
            //     render: function (data) {
            //         if (!data) return '<span class="text-muted">None</span>';
            //         return `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-secondary">
            //                     <i class="fa-solid fa-paperclip"></i> View
            //                 </a>`;
            //     },
            //     width: "100px",
            // },
            {
                data: "status",
                className: "text-center",
                width: "130px",
            },
            {
                data: "leave_id",
                className: "text-center",
                orderable: false,
                render: function (data) {
                    return `<button class="btn btn-sm btn-primary view-leave-btn" data-id="${data}">
                                <i class="fa-solid fa-eye"></i> View
                            </button>`;
                },
                width: "90px",
            },
        ],
    });

    $("#btn-refresh-leaves").on("click", function () {
        reloadTable("leaves_table");
    });

    $(document).on("click", ".view-leave-btn", function (e) {
        e.preventDefault();
        const rowData = dt.row($(this).closest("tr")).data();
        populateModal(rowData);
        $("#leaveDetailModal").modal("show");
    });

    function populateModal(row) {
        $("#rejectReasonSection").hide();
        $("#rejectReasonInput").val("").removeClass("is-invalid");
        $("#detail-confirm-reject-btn").hide();
        $("#detail-reject-btn").show();
        $("#detail-approve-btn").show();

        $("#detail-leave-id").val(row.leave_id);

        $("#detail-employee").text(row.employee   || "N/A");
        $("#detail-department").text(row.department || "N/A");
        $("#detail-position").text(row.position   || "N/A");

        $("#detail-start").text(row.start_date !== "N/A" ? formatDate(row.start_date) : "N/A");
        $("#detail-end").text(row.end_date   !== "N/A" ? formatDate(row.end_date)   : "N/A");
        $("#detail-days").text((row.days_count || 0) + " working day(s)");

        const isPending = row.status_id === 11;
        if (isPending) {
            $("#detail-pay-type").hide();
            $("#pay-type-toggle").show();
            $(`input[name="approve-pay-type"][value="${row.is_paid ? 1 : 0}"]`).prop("checked", true);
        } else {
            const payBadge = row.is_paid
                ? '<span class="badge bg-success">Paid Leave</span>'
                : '<span class="badge bg-warning text-dark">Unpaid Leave</span>';
            $("#detail-pay-type").html(payBadge).show();
            $("#pay-type-toggle").hide();
        }

        $("#detail-reason").text(row.reason || "N/A");
        $("#detail-status").html(row.status || "N/A");

        if (row.attachment_url) {
            $("#detail-attachment").html(
                `<a href="${row.attachment_url}" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="fa-solid fa-paperclip"></i> View
                </a>`
            );
        } else {
            $("#detail-attachment").html('<span class="text-muted">None</span>');
        }

        $("#detail-pending-actions").toggle(isPending);
    }

    $("#detail-reject-btn").on("click", function () {
        $("#rejectReasonSection").show();
        $("#detail-approve-btn").hide();
        $(this).hide();
        $("#detail-confirm-reject-btn").show();
    });

    $("#detail-confirm-reject-btn").on("click", function () {
        const reason = $("#rejectReasonInput").val().trim();
        if (!reason) {
            $("#rejectReasonInput").addClass("is-invalid");
            return;
        }
        $("#rejectReasonInput").removeClass("is-invalid");
        const leaveId = $("#detail-leave-id").val();
        submitAction(
            `/human-resources/leave/reject/${leaveId}`,
            { reason },
            "Rejected!",
            "Leave request rejected successfully."
        );
    });

    $("#detail-approve-btn").on("click", function () {
        const isPaid = $('input[name="approve-pay-type"]:checked').val() ?? "1";
        const payLabel = isPaid === "1" ? "Paid Leave" : "Unpaid Leave";
        Swal.fire({
            title: "Confirm Approval",
            html: `Approve this leave request as <strong>${payLabel}</strong>?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Approve",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction(
                    `/human-resources/leave/approve/${$("#detail-leave-id").val()}`,
                    { is_paid: isPaid },
                    "Approved!",
                    "Leave request approved successfully."
                );
            }
        });
    });

    function submitAction(url, extraData, successTitle, successMsg) {
        $("#LoadingScreen").fadeIn(200);
        $.ajax({
            url,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                ...extraData,
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                if (response.success) {
                    $("#leaveDetailModal").modal("hide");
                    reloadTable("leaves_table");
                    Toast.fire(successTitle, response.message, "success");
                } else {
                    Toast.fire("Error", response.message, "error");
                }
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                Toast.fire(
                    "Error",
                    xhr.responseJSON?.message || "Something went wrong.",
                    "error"
                );
            },
        });
    }

    // Reset modal state on close
    $("#leaveDetailModal").on("hidden.bs.modal", function () {
        $("#rejectReasonSection").hide();
        $("#rejectReasonInput").val("").removeClass("is-invalid");
        $("#detail-confirm-reject-btn").hide();
        $("#detail-reject-btn").show();
        $("#detail-approve-btn").show();
        $("#pay-type-toggle").hide();
        $("#detail-pay-type").show();
        $("#approve-paid").prop("checked", true);
    });
});
