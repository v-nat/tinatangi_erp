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
        // console.log("Approving overtime ID:", overtimeId);
        // console.log('clicked');
        $("#ApprovalConfirmation").modal("show");
    });
    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        // var overtimeId = $(this).data('id');
        // $('#approvalOvertimeId').val(overtimeId);
        // console.log('clicked');
        $("#RejectionConfirmation").modal("show");
    });

    $("#approve-btn-confirmed").click(function (e) {
        e.preventDefault();
        var overtimeId = $("#approvalOvertimeId").val();
        // console.log(overtimeId);
        let url = `/humanresources/overtime/approve/${id}`;

        $("#LoadingScreen").fadeIn(200);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            // url: "humanresources/update-employee/" + employee_id,
            url: url,
            type: "PUT",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                Swal.fire({
                    title: "Success!",
                    text: response.message,
                    icon: "success",
                }).then(() => location.reload());
            },
            error: function (xhr) {
                // console.error('Error response:', xhr);
                $("#LoadingScreen").fadeOut(200);
                if (xhr.responseJSON?.errors) {
                    let errorMessages = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("\n");
                    Swal.fire("Validation Error", errorMessages, "error");
                } else {
                    Swal.fire(
                        "Error",
                        "An unexpected error occurred.",
                        "error"
                    );
                }
            },
        });
    });
});
