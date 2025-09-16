$(document).ready(function () {
    const id = $("#employee_id").val();
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }
    function formatTime(timeString) {
        if (!timeString) return "--:--";
        const [h, m, s] = timeString.split(":");
        return `${h.padStart(2, "0")}:${m}`;
    }
    function formatMinutesToHours(minutes) {
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        return `${hours}h ${mins}m`;
    }
    $("#otRequests").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/overtimes/requests/list/" + id,
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
            {
                data: "date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
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
                data: "total_minutes",
                render: function (data) {
                    return data ? formatMinutesToHours(data) : "N/A";
                },
            },
            { data: "reason" },
            {
                data: "status",
                className: "text-center",
            },
        ],
    });

    $("#time_start, #time_end").on("change", function () {
        const start = $("#time_start").val();
        const end = $("#time_end").val();

        if (start && end && start > end) {
            Swal.fire({
                title: "Warning!",
                text: "Time End should not be earlier than Time Start",
                icon: "warning",
            });
            $("#time_end").val(""); // optional: clear invalid input
        }
        if (start > end) {
            $("#reqOt").prop("disabled", true);
        } else {
            $("#reqOt").prop("disabled", false);
        }
    });

    $("#reqOt").click(function (e) {
        e.preventDefault();

        let formData = new FormData($("#otApplication")[0]);
        Swal.fire({
            title: "Confirm Request",
            text: "You are about to submit an overtime application.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Submit",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    url: "/overtimes/request/submit",
                    type: "POST",
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
                            let errorMessages = Object.values(
                                xhr.responseJSON.errors
                            )
                                .flat()
                                .join("\n");
                            Swal.fire(
                                "Validation Error",
                                errorMessages,
                                "error"
                            );
                        } else {
                            Swal.fire(
                                "Error",
                                "An unexpected error occurred.",
                                "error"
                            );
                        }
                    },
                });
            }
        });
    });
});
