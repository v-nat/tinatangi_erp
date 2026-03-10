import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    const getThemeColor = (colorName) =>
        getComputedStyle(document.documentElement)
            .getPropertyValue(`--bs-${colorName}`)
            .trim();

    const formatCurrency = (val) => {
        if (val === null || val === undefined || isNaN(val)) return "₱ 0.00";
        return (
            "₱ " +
            parseFloat(val).toLocaleString("en-PH", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    };

    let budgetChart = null;

    function initChart() {
        const chartEl = document.getElementById("budget-release-chart");
        if (!chartEl) return;

        const options = {
            series: [],
            chart: {
                type: "bar",
                height: 320,
                toolbar: { show: false },
                noData: { text: "Loading chart data..." },
            },
            plotOptions: {
                bar: {
                    columnWidth: "55%",
                    endingShape: "rounded",
                },
            },
            stroke: {
                show: true,
                width: 2,
                colors: ["transparent"],
            },
            dataLabels: { enabled: false },
            colors: [getThemeColor("primary"), getThemeColor("success")],
            xaxis: {
                categories: [],
            },
            yaxis: {
                labels: {
                    formatter: (value) => (value / 1000).toFixed(1) + "k",
                },
            },
            tooltip: {
                y: {
                    formatter: (value) => formatCurrency(value),
                },
            },
            legend: {
                position: "top",
                horizontalAlign: "right",
            },
        };

        budgetChart = new ApexCharts(chartEl, options);
        budgetChart.render();
    }

    function loadChart() {
        if (!budgetChart) return;
        $.ajax({
            url: "/finance/budgets/summary",
            type: "GET",
            dataType: "json",
            success: function (response) {
                const safeResponse = response || {};

                budgetChart.updateOptions({
                    xaxis: { categories: response.labels || [] },
                });
                budgetChart.updateSeries(response.series || []);
            },
            error: function (xhr) {
                console.error(
                    "Failed to load budget summary:",
                    xhr.responseText
                );
            },
        });
    }

    initChart();
    loadChart();

    $("#refresh-approvals").on("click", function () {
        reloadTable("approvalTable");
    });

    $("#approvalTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/finance/budgets/requests",
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
            { data: "type", className: "dt-left" },
            {
                data: "amount",
                className: "dt-left",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            { data: "requested_by_id", className: "dt-left" },
            { data: "requested_at", className: "dt-left" },
            { data: "department", className: "dt-left" },
            { data: "notes", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (
                        row.status !==
                        '<span class="badge bg-warning">Pending</span>'
                    ) {
                        return " ";
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 approve-btn"
                                data-id="${data}"
                                data-request-id="${row.request_id}"
                                title="Approve">
                                    <i class="fa-solid fa-check"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                data-request-id="${row.request_id}"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                            </a>
                        </div>
                        `;
                    }
                },
                className: "text-center",
                width: "150px",
            },
        ],
    });

    $("#pr_finance_type_filter").on("change", function () {
        const selectedType = $(this).val();
        releaseTable
            .column(2)
            .search(selectedType ? "^" + selectedType + "$" : "", true, false)
            .draw();
    });

    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const request_id = $(this).data("request-id");
        $("#rejectionReleaseId").val(id);
        $("#rejectionRequestId").val(request_id);
        $("#RejectionConfirmation").modal("show");
    });

    $(document).on("click", ".approve-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const request_id = $(this).data("request-id");

        Swal.fire({
            title: "Approve Request?",
            text: "You are about to release a budget for this request.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm!",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: `/finance/budgets/requests/approve/${id}/${request_id}`,
                type: "PUT",
                data: null,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("approvalTable");
                    reloadTable("historyTable");
                    loadChart();
                    Toast.fire({
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
                        Toast.fire("Validation Error", errorMessages, "error");
                    } else {
                        Toast.fire(
                            "Error",
                            "An unexpected error occurred.",
                            "error"
                        );
                    }
                },
            });
        });
    });

    $("#reject-btn-confirmed").click(function (e) {
        e.preventDefault();
        let id = $("#rejectionReleaseId").val();
        let notes = $("#rejectionNotes").val();
        let request_id = $("#rejectionRequestId").val();
        if (notes) {
            $("#LoadingScreen").fadeIn(200);
            $("#rejectionModal").modal("hide");
            $.ajax({
                url: `/finance/budgets/requests/reject/${id}`,
                method: "PUT",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    id: id,
                    notes: notes,
                    request_id: request_id,
                },
                success: function (response) {
                    if (response.success) {
                        $("#LoadingScreen").fadeOut(200);
                        reloadTable("approvalTable");
                        reloadTable("historyTable");
                        loadChart();
                        Toast.fire("Rejected!", response.message, "success");
                    } else {
                        Toast.fire("Error", response.message, "error");
                    }
                },
                error: function (xhr) {
                    Toast.fire(
                        "Error",
                        xhr.responseJSON?.message || "Something went wrong",
                        "error"
                    );
                },
            });
        } else {
            Toast.fire({
                icon: "error",
                title: "Error",
                text: "Please provide a remarks",
                timer: 1500,
            });
        }
    });

    const releaseTable = $("#historyTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/finance/budgets/history",
            type: "GET",
            dataSrc: "data",
        },
        dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rtip',
        buttons: [
            {
                extend: "print",
                title: "",
                text: '<i class="fa-solid fa-print"></i> Print',
                className: "btn btn-sm btn-primary",
                exportOptions: {
                    columns: ":visible:not(:eq(0)):not(:eq(-1))",
                },
                customize: function (win) {
                    win.document.title =
                        "https://tinatangi.site/finance/budgets/";
                    const now = new Date();
                    const dateStr =
                        now.getFullYear() +
                        "-" +
                        (now.getMonth() + 1).toString().padStart(2, "0") +
                        "-" +
                        now.getDate().toString().padStart(2, "0");

                    win.document.title = `${dateStr}-finance-budget-release-records`;

                    $(win.document.head).append(
                        "<style>" +
                            "body { font-family: Arial, sans-serif; font-size: 8pt; }" +
                            ".print-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ccc; }" +
                            ".print-header-left { font-weight: bold; }" +
                            ".print-header-right { font-size: 8pt; color: #555; }" +
                            ".print-title { text-align: center; font-size: 12pt; font-weight: bold; margin-bottom: 5px; }" +
                            ".print-subtitle { text-align: center; font-size: 10pt; font-weight: normal; margin-bottom: 20px; }" +
                            "table { width: 100%; margin-top: 20px; border: 2px solid #000; }" +
                            "table th, table td { text-align: left; padding: 4px; border: 2px solid #000; }" +
                            "</style>"
                    );

                    $(win.document.body).prepend(
                        '<div class="print-header">' +
                            '<div class="print-header-left">Tinatangi Cafe ERP Management System</div>' +
                            '<div class="print-header-right">' +
                            new Date().toLocaleString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                                hour: "2-digit",
                                minute: "2-digit",
                            }) +
                            "</div>" +
                            "</div>" +
                            '<div class="print-title">Tinatangi Cafe | Finance and Accounting Department</div>' +
                            '<div class="print-subtitle">Budget Release Records</div>'
                    );

                    $(win.document.body).find("h1").remove();
                    $(win.document.body)
                        .find("table")
                        .removeClass("dataTable")
                        .addClass("compact")
                        .css("font-size", "inherit");
                },
            },
        ],
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
                width: "45px",
            },
            { data: "release_id", className: "dt-left" },
            { data: "type", className: "dt-left" },
            {
                data: "amount",
                className: "dt-left",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            { data: "requested_by_id", className: "dt-left" },
            { data: "requested_at", className: "dt-left" },
            { data: "department", className: "dt-left" },
            { data: "released_by_id", className: "dt-left" },
            { data: "released_at", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
        ],
        initComplete: function () {
            const column = this.api().column(2);
            const select = $("#pr_finance_type_filter");

            column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        select.append(
                            $("<option></option>").attr("value", d).text(d)
                        );
                    }
                });

            loadChart();
        },
    });
});
