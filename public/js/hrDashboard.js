import {
    formatDate,
    formatMinutesToHours,
} from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    $("#attendanceTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/human-resources/attendance/list",
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
                type: "date", // Ensure proper date sorting
            },
            {
                data: "time_in",
                className: "dt-left",
                type: "date", // Ensure proper time sorting
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
});
