import { reloadTable } from "./utils/reloadTable.js";
import { formatDate, formatTime } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    $("#overtime_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/human-resources/overtimes/get",
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
                data: "date",
                className: "dt-left",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date",
            },
            {
                data: "time_start",
                className: "dt-left",
                render: function (data) {
                    return data ? formatTime(data) : "N/A";
                },
                type: "time",
            },
            {
                data: "time_end",
                className: "dt-left",
                render: function (data) {
                    return data ? formatTime(data) : "N/A";
                },
                type: "time",
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
                data: "overtime_id",
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
                                data-id="${row.overtime_id}"
                                title="Approve">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                                <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                data-name="${row.name}"
                                data-overtime-id="${row.overtime_id}"
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

    $("#btn-refresh-overtimes").on("click", function () {
        reloadTable("overtime_table");
    });

    $(".modal").on("hidden.bs.modal", function () {
        $(this).find("form").trigger("reset");
    });
    $(document).on("click", ".approve-btn", function (e) {
        e.preventDefault();
        const overtimeId = $(this).data("id");
        $("#approvalOvertimeId").val(overtimeId);
        $("#ApprovalConfirmation").modal("show");
    });
    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        const overtimeId = $(this).data("id");
        $("#rejectionOvertimeId").val(overtimeId);
        $("#RejectionConfirmation").modal("show");
    });

    $("#approve-btn-confirmed").click(function (e) {
        e.preventDefault();
        let overtimeId = $("#approvalOvertimeId").val();
        let reason = $("#approvalNotes").val();
        $("#LoadingScreen").fadeIn(200);
        $("#approvalModal").modal("hide");
        $.ajax({
            url: `/human-resources/overtime/approve/${overtimeId}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: overtimeId,
                reason: reason,
            },
            success: function (response) {
                if (response.success) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("overtime_table");
                    Toast.fire("Approved!", response.message, "success");
                } else {
                    Toast.fire("Error", response.message, "error");
                }
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
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
        let overtimeId = $("#rejectionOvertimeId").val();
        let reason = $("#rejectionNotes").val();
        $("#LoadingScreen").fadeIn(200);
        $("#rejectionModal").modal("hide");

        $.ajax({
            url: `/human-resources/overtime/reject/${overtimeId}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: overtimeId,
                reason: reason,
            },
            success: function (response) {
                if (response.success) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("overtime_table");
                    Toast.fire("Rejected!", response.message, "success");
                } else {
                    Toast.fire("Error", response.message, "error");
                }
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                Toast.fire(
                    "Error",
                    xhr.responseJSON?.message || "Something went wrong",
                    "error"
                );
            },
        });
    });
});
