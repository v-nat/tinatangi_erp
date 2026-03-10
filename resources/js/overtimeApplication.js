import { reloadTable } from "./utils/reloadTable.js";
import {
    formatDate,
    formatTime,
    formatMinutesToHours,
    getTodayDateString,
} from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const id = $("#employee_id").val();
    $("#otRequests").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/employee/overtimes/requests/list/" + id,
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
                data: "date",
                className: "dt-left",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
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
                data: "total_minutes",
                className: "dt-left",
                render: function (data) {
                    return data ? formatMinutesToHours(data) : "N/A";
                },
            },
            { data: "reason", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
        ],
    });
    $("#btn-refresh-ot-requests").on("click", function () {
        reloadTable("otRequests");
    });

    $("#time_start, #time_end").on("change", function () {
        const start = $("#time_start").val();
        const end = $("#time_end").val();

        if (start && end && start > end) {
            Toast.fire({
                title: "Warning!",
                text: "Time End should not be earlier than Time Start",
                icon: "warning",
            });
            $("#time_end").val("");
        }
        if (start > end) {
            $("#reqOt").prop("disabled", true);
        } else {
            $("#reqOt").prop("disabled", false);
        }
    });

    $("#date").attr("max", getTodayDateString());

    $("#reqOt").click(function (e) {
        e.preventDefault();
        let isValid = true;
        const $form = $("#otApplication");

        $form.find("input, select, option").each(function () {
            const $field = $(this);
            const value = $field.val();
            if ($field.prop("required") && (!value || !value.trim())) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        if (isValid) {
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
                        url: "/employee/overtimes/request/submit",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $("#LoadingScreen").fadeOut(200);
                            $("#otApplication").trigger("reset");
                            reloadTable("otRequests");
                            Toast.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                            });
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
                                Toast.fire(
                                    "Validation Error",
                                    errorMessages,
                                    "error"
                                );
                            } else {
                                Toast.fire({
                                    text: xhr.responseJSON.error,
                                    icon: "error",
                                    timer: 3000
                                });
                            }
                        },
                    });
                }
            });
        }
    });
});
