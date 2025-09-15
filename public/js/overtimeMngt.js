$(document).ready(function () {
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }
    function formatTime(timeString) {
        if (!timeString) return "--:--";
        const [h, m, s] = timeString.split(":");
        return `${h.padStart(2, "0")}:${m}`;
    }

    $("#overtime_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/humanresources/overtimes/get",
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
            },
            { data: "employee" },
            { data: "department" },
            { data: "position" },
            {
                data: "date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date",
            },
            {
                data: "time_start",
                render: function (data) {
                    return data ? formatTime(data) : "N/A";
                },
                type: "time",
            },
            {
                data: "time_end",
                render: function (data) {
                    return data ? formatTime(data) : "N/A";
                },
                type: "time",
            },
            {
                data: "reason",
            },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "overtime_id",
                render: function (data, type, row) {
                    if (row.status !== '<span class="badge bg-warning">Pending</span>') {
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
            },
        ],
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
            url: `/humanresources/overtime/approve/${overtimeId}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: overtimeId,
                reason: reason,
            },
            success: function (response) {
                if (response.success) {
                    updateOvertimeStatus(overtimeId, 13, "Approved", reason);
                    Swal.fire("Approved!", response.message, "success");
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function (xhr) {
                Swal.fire(
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
        // console.log(overtimeId);
        // let url = `/humanresources/overtime/approve/${overtimeId}`;
        // const url = "{{ route('hr.ot-approve', id) }}";
        $.ajax({
            url: `/humanresources/overtime/reject/${overtimeId}`,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: overtimeId,
                reason: reason,
            },
            success: function (response) {
                if (response.success) {
                    updateOvertimeStatus(overtimeId, 13, "Rejected", reason);
                    Swal.fire("Rejected!", response.message, "success");
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function (xhr) {
                Swal.fire(
                    "Error",
                    xhr.responseJSON?.message || "Something went wrong",
                    "error"
                );
            },
        });
    });

    function updateOvertimeStatus(overtimeId, status, statusText, reason) {
        $("#approvalModal").modal("hide");
        $("#LoadingScreen").fadeOut(200);
        var row = $('a.approve-btn[data-id="' + overtimeId + '"]').closest(
            "tr"
        );
        var statusBadgeClass = status === 13 ? "bg-success" : "bg-danger";
        row.find("td:nth-child(9)").html(
            '<span class="badge ' +
                statusBadgeClass +
                '">' +
                statusText +
                "</span>"
        );

        row.find(".approve-btn, .reject-btn").remove();

        row.data("updated", {
            status: status,
            reason: reason || "",
            approved_by: "{{ Auth::user()->full_name }}",
            approved_at: new Date().toLocaleString(),
        });
    }
});
