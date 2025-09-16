$(document).ready(function () {
    const id = $("#employee_id").val();
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }
    $("#leaveRequests").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/leaves/requests/list/" + id,
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
                data: "start_date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
            },
            {
                data: "end_date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
            },
            { data: "reason" },
            {
                data: "status",
                className: "text-center",
            },
        ],
    });
    $("#textAreaDiv").hide();
    $("#textAreaReason").prop("required", false);
    $("#reason").change(function () {
        if ($(this).val() === "Other") {
            $("#textAreaDiv").show();
            $("#textAreaReason").prop("required", true);
        } else {
            $("#textAreaDiv").hide();
            $("#textAreaReason").prop("required", false);
        }
    });

    $("#start_date, #end_date").on("change", function () {
        const start = $("#start_date").val();
        const end = $("#end_date").val();

        if (start && end && start > end) {
            Swal.fire({
                title: "Warning!",
                text: "Date End should not be earlier than Date Start",
                icon: "warning",
            });
            $("#end_date").val(""); // optional: clear invalid input
        }
        if (start > end) {
            $("#reqLeave").prop("disabled", true);
        } else {
            $("#reqLeave").prop("disabled", false);
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
