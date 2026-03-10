import { formatDate, formatMinutesToHours } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    // ─── Leave Calendar ──────────────────────────────────────────────────────
    var calendarEl = $("#calendar")[0];
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        themeSystem: "bootstrap5",
        events: calendarEl.dataset.eventsUrl,
    });
    calendar.render();

    // ─── Today's Attendance DataTable ────────────────────────────────────────
    const attendanceTable = $("#attendanceTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/human-resources/attendance/list",
            type: "GET",
            dataSrc: "data",
            error: function (xhr) {
                console.error("Error fetching data:", xhr.responseText);
            },
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) { return meta.row + 1; },
                className: "text-center",
                width: "45px",
            },
            { data: "name", className: "dt-left" },
            {
                data: "date",
                className: "dt-left",
                render: function (data) { return data ? formatDate(data) : "N/A"; },
                type: "date",
            },
            { data: "time_in",  className: "dt-left", type: "date" },
            { data: "time_out", className: "dt-left" },
            {
                data: "total_minutes",
                className: "dt-left",
                render: function (data) { return data ? formatMinutesToHours(data) : "N/A"; },
            },
            { data: "overtime", className: "dt-left" },
            { data: "status",   className: "text-center", width: "150px" },
        ],
    });

    $("#btn-refresh-dashboard-attendance").on("click", function () {
        attendanceTable.ajax.reload(null, false);
    });

    // ─── Analytics Charts ────────────────────────────────────────────────────
    $.get("/human-resources/dashboard/analytics", function (data) {
        renderWeeklyAttendance(data.weekly_attendance);
        renderDepartmentHeadcount(data.department_headcount);
        renderOvertimeTrend(data.overtime_trend);
        renderPayrollTrend(data.payroll_trend);
    }).fail(function (xhr) {
        console.error("Failed to load HR analytics:", xhr.responseText);
    });
});

// ── Weekly Attendance Stacked Bar ─────────────────────────────────────────────
function renderWeeklyAttendance(d) {
    new ApexCharts(document.querySelector("#chart-weekly-attendance"), {
        chart:  { type: "bar", height: 280, stacked: true, toolbar: { show: false } },
        series: [
            { name: "On Time",  data: d.on_time  },
            { name: "Late",     data: d.late     },
            { name: "On Leave", data: d.on_leave },
        ],
        xaxis:  { categories: d.days },
        colors: ["#28a745", "#ffc107", "#dc3545"],
        plotOptions: { bar: { columnWidth: "50%", borderRadius: 4 } },
        legend: { position: "top" },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: (v) => v + " employee(s)" } },
    }).render();
}

// ── Department Headcount Donut ────────────────────────────────────────────────
function renderDepartmentHeadcount(d) {
    new ApexCharts(document.querySelector("#chart-department-headcount"), {
        chart:    { type: "donut", height: 280 },
        series:   d.counts,
        labels:   d.labels,
        legend:   { position: "bottom" },
        dataLabels: { enabled: true },
        tooltip:  { y: { formatter: (v) => v + " employee(s)" } },
        plotOptions: { pie: { donut: { size: "60%" } } },
    }).render();
}

// ── Overtime Trend Line ───────────────────────────────────────────────────────
function renderOvertimeTrend(d) {
    new ApexCharts(document.querySelector("#chart-overtime-trend"), {
        chart:  { type: "line", height: 260, toolbar: { show: false }, zoom: { enabled: false } },
        series: [{ name: "OT Requests", data: d.counts }],
        xaxis:  { categories: d.labels },
        colors: ["#6f42c1"],
        stroke: { curve: "smooth", width: 3 },
        markers: { size: 5 },
        dataLabels: { enabled: false },
        tooltip:  { y: { formatter: (v) => v + " request(s)" } },
    }).render();
}

// ── Payroll Trend Bar ─────────────────────────────────────────────────────────
function renderPayrollTrend(d) {
    new ApexCharts(document.querySelector("#chart-payroll-trend"), {
        chart:  { type: "bar", height: 260, toolbar: { show: false } },
        series: [{ name: "Net Payroll", data: d.totals }],
        xaxis:  { categories: d.labels },
        colors: ["#0d6efd"],
        plotOptions: { bar: { columnWidth: "50%", borderRadius: 4 } },
        dataLabels: { enabled: false },
        tooltip: {
            y: {
                formatter: (v) =>
                    "₱" + parseFloat(v).toLocaleString("en-PH", { minimumFractionDigits: 2 }),
            },
        },
        yaxis: {
            labels: {
                formatter: (v) => "₱" + parseFloat(v).toLocaleString("en-PH"),
            },
        },
    }).render();
}
