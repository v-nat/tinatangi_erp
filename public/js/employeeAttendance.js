$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Helper functions
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

    function showError(message) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: message,
            timer: 3000,
        });
    }

    function showSuccess(message) {
        Swal.fire({
            icon: "success",
            title: "Success",
            text: message,
            timer: 3000,
        });
    }
    let isTimeIn = false;
    let timeIn = null;

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
        if (isTimeIn) {
            $("#totalHours").text(calculateMinutesWorked(formatTime(timeIn)));
        }
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
        const diffMinutes = Math.floor(diffMs / 60000); // 60000 ms = 1 minute

        return diffMinutes;
    }

    function hasAttendanceToday() {
        $.get("/attendance/this-day")
            .done(function (response) {
                if (response.success) {
                    const data = response.data;
                    if (data) {
                        $("#timeInDisplay").text(formatTime(data.time_in));
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
                        response.message || "Failed to load today's attendance"
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
    // Initial check
    hasAttendanceToday();

    // Time In Handler
    $("#timeInBtn").click(function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Confirm Time In?",
            text: "This will record your current time as Time In",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Time In",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("/attendance/time-in")
                    .done(function (response) {
                        hasAttendanceToday(); // Refresh the display
                        timeIn = $("#timeInDisplay").text;
                        isTimeIn = true;
                        $("#attendanceTable")
                            .DataTable()
                            .ajax.reload(null, false); // Preserve current page
                        showSuccess("Time in recorded successfully!");
                    })
                    .fail(function (xhr) {
                        showError(
                            xhr.responseJSON?.message ||
                                "Failed to record Time In"
                        );
                    });
            }
        });
    });

    // Time Out Handler
    $("#timeOutBtn").click(function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Confirm Time Out?",
            text: "This will record your current time as Time Out",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Time Out",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("/attendance/time-out")
                    .done(function (response) {
                        hasAttendanceToday(); // Refresh the display
                        isTimeIn = false;
                        $("#attendanceTable")
                            .DataTable()
                            .ajax.reload(null, false); // Preserve current page
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

    $("#attendanceTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/attendance/list",
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
            },
            {
                data: "name",
            },
            {
                data: "date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
            },
            {
                data: "time_in",
                type: "date", // Ensure proper time sorting
            },
            {
                data: "time_out",
            },
            {
                data: "total_minutes",
                render: function (data) {
                    return data ? formatMinutesToHours(data) : "N/A";
                },
            },
            {
                data: "overtime",
            },
            {
                data: "tardiness",
            },
            {
                data: "leave_info",
            },
            {
                data: "status",
                className: "text-center",
            },
        ]
    });
});
