import $ from 'jquery';
import Swal from 'sweetalert2';
import {
    formatDate,
    formatTime,
    formatMinutesToHours,
} from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $(".showAttendanceModal").click(function (e) {
        e.preventDefault();
        $("#employeeAttendance").modal("show");
    });

    function showError(message) {
        Toast.fire({
            icon: "error",
            title: "Error",
            text: message,
            timer: 2000,
        });
    }

    function showSuccess(message) {
        Toast.fire({
            icon: "success",
            title: "Success",
            text: message,
            timer: 1500,
        });
    }

    function getCurrentManilaTime() {
        const options = {
            timeZone: "Asia/Manila",
            hour12: false,
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
        };
        return new Date().toLocaleString("en-US", options);
    }

    // CLOCK
    function updateClock() {
        $("#realtimeClock").text(getCurrentManilaTime());
    }
    setInterval(updateClock, 1000);
    updateClock();

    function calculateMinutesWorked(timeInString) {
        const timeIn = new Date(timeInString);
        const now = new Date();
        const manilaNow = new Date(
            now.toLocaleString("en-US", { timeZone: "Asia/Manila" })
        );
        const diffMs = manilaNow - timeIn;
        const diffMinutes = Math.floor(diffMs / 60000);

        return diffMinutes;
    }

    function isOnLeaveToday() {
        return $.get("/attendance/isOnLeave")
            .then(function (response) {
                if (response.success && response.data) {
                    $("#timeOutBtn").prop("disabled", true);
                    $("#timeInBtn").prop("disabled", false);
                    return true;
                }
                return false;
            })
            .catch(function (xhr) {
                console.error(
                    xhr.responseJSON?.message ||
                        "Failed to check today's attendance"
                );
                return false;
            });
    }

    function hasAttendanceToday() {
        isOnLeaveToday().then(function (isLeave) {
            if (isLeave) {
                $("#timeInBtn").prop("disabled", false);
                $("#timeOutBtn").prop("disabled", true);
                $("#timeInDisplay").text("--:-- --");
                $("#timeOutDisplay").text("--:-- --");
                $("#totalHours").text("0h 0m");
            } else {
                $.get("/attendance/this-day")
                    .done(function (response) {
                        if (response.success) {
                            const data = response.data;
                            if (data) {
                                $("#timeInDisplay").text(
                                    formatTime(data.time_in)
                                );
                                if (data.time_out) {
                                    $("#timeOutDisplay").text(
                                        formatTime(data.time_out)
                                    );
                                    $("#totalHours").text(
                                        formatMinutesToHours(data.hours_worked)
                                    );
                                    $("#timeOutBtn").prop("disabled", true);
                                    $("#timeInBtn").prop("disabled", true);
                                } else {
                                    $("#timeOutBtn").prop("disabled", false);
                                    $("#timeInBtn").prop("disabled", true);
                                }
                            } else {
                                $("#timeInBtn").prop("disabled", false);
                                $("#timeOutBtn").prop("disabled", true);
                                $("#timeInDisplay").text("--:-- --");
                                $("#timeOutDisplay").text("--:-- --");
                                $("#totalHours").text("0h 0m");
                            }
                        } else {
                            showError(
                                response.message ||
                                    "Failed to load today's attendance"
                            );
                        }
                    })
                    .fail(function (xhr) {
                        showError(
                            xhr.responseJSON?.message ||
                                "Failed to check today's attendance"
                        );
                    });
            }
        });
    }

    hasAttendanceToday();

    $("#timeInBtn").click(function (e) {
        e.preventDefault();
        isOnLeaveToday().then(function (isLeave) {
            if (isLeave) {
                Swal.fire({
                    icon: "warning",
                    title: "You're on Leave",
                    text: "",
                    timer: 3000,
                });
            } else {
                Swal.fire({
                    title: "Confirm Time In?",
                    text: "This will count as your Time In",
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: "Time In",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#LoadingScreen").fadeIn(200);
                        $.post("/attendance/time-in")
                            .done(function (response) {
                                hasAttendanceToday();
                                $("#employeeAttendance").modal("hide");
                                $("#LoadingScreen").fadeOut(200);
                                showSuccess("Time in recorded successfully!");
                            })
                            .fail(function (xhr) {
                                showError(
                                    xhr.responseJSON?.message ||
                                        "Failed to record Time In"
                                );
                            });
                        $("#LoadingScreen").fadeOut(200);
                    }
                });
            }
        });
    });

    $("#timeOutBtn").click(function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Confirm Time Out?",
            text: "You are about to Time Out",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Continue",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);
                $.post("/attendance/time-out")
                    .done(function (response) {
                        hasAttendanceToday();
                        $("#employeeAttendance").modal("hide");
                        $("#LoadingScreen").fadeOut(200);
                        showSuccess("Time out recorded successfully!");
                    })
                    .fail(function (xhr) {
                        showError(
                            xhr.responseJSON?.message ||
                                "Failed to record Time Out"
                        );
                    });
            }
        });
    });

    const id = $("#employee_id").text().trim();

    const attendanceTable = $("#employeeAttendanceTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/attendance/get-employee-attendance-list/" + id,
            type: "GET",
            dataSrc: "data",
            error: function (xhr) {
                console.error("Error fetching data:", xhr.responseText);
                showError("Failed to load attendance data");
            },
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
                data: "name",
                className: "dt-left",
            },
            {
                data: "date",
                className: "dt-left",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date",
            },
            {
                data: "time_in",
                className: "dt-left",
                type: "date",
            },
            {
                data: "time_out",
                className: "dt-left",
            },
            {
                data: "total_minutes",
                className: "dt-left",
                render: function (data) {
                    return data ? formatMinutesToHours(data) : "N/A";
                },
            },
            {
                data: "overtime",
                className: "dt-left",
            },
            {
                data: "tardiness",
                className: "dt-left",
            },
            {
                data: "leave_info",
                className: "dt-left",
            },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
        ],
    });

    $("#btn-refresh-employee-attendance").on("click", function () {
        attendanceTable.ajax.reload(null, false);
    });
});
