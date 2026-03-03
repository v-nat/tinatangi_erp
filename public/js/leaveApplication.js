import { reloadTable } from "./utils/reloadTable.js";
import { formatDate } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const id = $("#employee_id").val();
    $("#leaveRequests").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/employee/leaves/requests/list/" + id,
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
            { data: "reason", className: "dt-left" },
            {
                data: "attachment_url",
                className: "text-center",
                orderable: false,
                render: function (data) {
                    if (!data) return '<span class="text-muted">None</span>';
                    return `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-paperclip"></i> View
                            </a>`;
                },
                width: "100px",
            },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
        ],
    });
    $("#btn-refresh-leave-requests").on("click", function () {
        reloadTable("leaveRequests");
    });
    const today = new Date().toISOString().split("T")[0];
    $("#start_date, #end_date").attr("min", today);
    $("#textAreaDiv").hide();
    $("#textAreaReason").prop("required", false);
    $("#reason").change(function () {
        if ($(this).val() === "Other") {
            $("#reasonDiv").removeClass("mb-10").addClass("mb-3");
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
            Toast.fire({
                title: "Warning!",
                text: "Date End should not be earlier than Date Start",
                icon: "warning",
            });
            $("#end_date").val("");
        }
        if (start > end) {
            $("#reqLeave").prop("disabled", true);
        } else {
            $("#reqLeave").prop("disabled", false);
        }
    });

    $("#reqLeave").on("click", async function (e) {
        e.preventDefault();

        const selectedReason = $("#reason").val();
        const textAreaReason = $("#textAreaReason").val();

        if (selectedReason === "Other" && textAreaReason.length < 1) {
            Toast.fire({
                title: "Missing Reason",
                text: "Please provide a reason in the text area.",
                icon: "warning",
            });
            return;
        }

        if (selectedReason === "Other" && textAreaReason) {
            const customReason = "Other: " + textAreaReason;
            if (
                $("#reason option[value='" + customReason + "']").length === 0
            ) {
                $("#reason").append(new Option(customReason, customReason));
            }
            $("#reason").val(customReason);
        } else {
            $("#reason").val(selectedReason);
        }

        let isValid = true;
        const $form = $("#leaveApplication");

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

        const attachmentFile = document.getElementById("attachment").files[0];
        if (!attachmentFile) {
            $("#attachment").addClass("is-invalid");
            Toast.fire({
                title: "Missing Attachment",
                text: "Please attach a supporting document (PDF, DOC, DOCX, JPG, or PNG).",
                icon: "warning",
            });
            return;
        } else {
            $("#attachment").removeClass("is-invalid");
        }

        if (!isValid) return;

        const today = new Date().toISOString().split("T")[0];
        const startDate = $("#start_date").val();
        const endDate = $("#end_date").val();

        if (startDate <= today && today <= endDate) {
            try {
                const attendance = await $.ajax({
                    url: "/attendance/this-day",
                    type: "GET",
                });
                if (
                    attendance.success &&
                    attendance.data &&
                    !attendance.data.time_out
                ) {
                    Toast.fire({
                        title: "Warning!",
                        text: "You are currently timed in. Please time out first before filing a leave for today.",
                        icon: "warning",
                    });
                    return;
                }
            } catch (err) {
                // allow submission to continue if the check fails
            }
        }

        let formData = new FormData($("#leaveApplication")[0]);
        Swal.fire({
            title: "Confirm Request",
            text: "You are about to submit a leave application.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Submit",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $(
                            'meta[name="csrf-token"]'
                        ).attr("content"),
                    },
                    url: "/employee/leaves/request/submit",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $("#LoadingScreen").fadeOut(200);
                        $("#leaveApplication").trigger("reset");
                        $("#attachment").val("");
                        reloadTable("leaveRequests");
                        Toast.fire({
                            title: "Success!",
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
                            Toast.fire(
                                "Validation Error",
                                errorMessages,
                                "error"
                            );
                        } else if (xhr.responseJSON?.error) {
                            Toast.fire(
                                "Warning!",
                                xhr.responseJSON.error,
                                "warning"
                            );
                        } else {
                            Toast.fire(
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
