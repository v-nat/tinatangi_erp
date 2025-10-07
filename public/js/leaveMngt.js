import { reloadTable } from "./utils/reloadTable.js";
import { formatDate } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    $("#leaves_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/humanresources/leaves/get",
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
                type: "date", // Ensure proper date sorting
            },
            {
                data: "end_date",
                className: "dt-left",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
            },

            {
                data: "reason",
                className: "dt-left",
            },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
            {
                data: "leave_id",
                render: function (data, type, row) {
                    if (
                        row.status !==
                        '<span class="badge bg-warning">Pending</span>'
                    ) {
                        return "";
                    } else {
                        return `
                            <div>
                                <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 approve-btn"
                                data-id="${row.leave_id}"
                                title="Approve">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                                <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                data-name="${row.name}"
                                data-leave-id="${row.leave_id}"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                                </a>
                            </div>
                        `;
                    }
                },
                width: "150px",
            },
        ],
    });

    $(".modal").on("hidden.bs.modal", function () {
        $(this).find("form").trigger("reset");
    });
    $(document).on("click", ".approve-btn", function (e) {
        e.preventDefault();
        const leaveId = $(this).data("id");
        $("#approvalLeaveId").val(leaveId);
        $("#ApprovalConfirmation").modal("show");
    });
    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        const leaveId = $(this).data("id");
        $("#rejectionLeaveId").val(leaveId);
        $("#RejectionConfirmation").modal("show");
    });

    $("#approve-btn-confirmed").click(function (e) {
        e.preventDefault();
        let leaveId = $("#approvalLeaveId").val();
        let reason = $("#approvalNotes").val();
        $("#LoadingScreen").fadeIn(200);
        $("#approvalModal").modal("hide");
        $.ajax({
            url: `/humanresources/leave/approve/${leaveId}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: leaveId,
                reason: reason,
            },
            success: function (response) {
                if (response.success) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("leaves_table");
                    Toast.fire("Approved!", response.message, "success");
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

    $("#reject-btn-confirmed").click(function (e) {
        e.preventDefault();
        let leaveId = $("#rejectionLeaveId").val();
        let reason = $("#rejectionNotes").val();
        $("#LoadingScreen").fadeIn(200);
        $("#rejectionModal").modal("hide");
        $.ajax({
            url: `/humanresources/leave/reject/${leaveId}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: leaveId,
                reason: reason,
            },
            success: function (response) {
                if (response.success) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("leaves_table");
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
});
