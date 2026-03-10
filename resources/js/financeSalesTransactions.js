import $ from 'jquery';
import Swal from 'sweetalert2';

$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    if (csrfToken) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
        });
    }

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

    const $statusFilter = $("#sales-status-filter");
    const $selectAllReports = $("#select-all-reports");
    const $approveBtn = $("#approve-sales-reports");
    const $rejectBtn = $("#reject-sales-reports");
    const $pendingCount = $("#sales-pending-count");
    const $approvedToday = $("#sales-approved-today");

    let salesChart = null;

    function initChart() {
        const chartEl = document.getElementById("finance-sales-activity");
        if (!chartEl) return;

        const options = {
            series: [],
            chart: {
                type: "line",
                height: 320,
                toolbar: { show: false },
                noData: { text: "Loading chart data..." },
            },
            stroke: {
                curve: "smooth",
                width: 3,
            },
            colors: [getThemeColor("primary"), getThemeColor("success")],
            dataLabels: { enabled: false },
            xaxis: {
                categories: [],
                title: { text: "Date" },
            },
            yaxis: {
                labels: {
                    formatter: (value) => {
                        return "₱ " + (value / 1000).toFixed(1) + "k";
                    },
                },
            },
            tooltip: {
                y: {
                    formatter: (value) => formatCurrency(value),
                },
            },
            legend: { position: "top", horizontalAlign: "right" },
        };

        salesChart = new ApexCharts(chartEl, options);
        salesChart.render();
    }

    function updateActionButtons() {
        const selectedCount = $(".sales-report-checkbox:checked").length;
        $approveBtn.prop("disabled", selectedCount === 0);
        $rejectBtn.prop("disabled", selectedCount === 0);
    }

    const salesTable = $("#finance-sales-table").DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false,
        ajax: {
            url: "/finance/sales-transactions/list",
            data: function (d) {
                d.status = $statusFilter.val();
            },
            dataSrc: "data",
        },
        order: [[6, "desc"]],
        columnDefs: [
            { targets: [0, 4, 9], orderable: false, searchable: false },
        ],
        columns: [
            {
                data: "id",
                render: function (data, type, row) {
                    const isSelectable = Number(row.status) === 11;
                    return `<input type="checkbox" class="form-check-input sales-report-checkbox" value="${data}" ${
                        isSelectable ? "" : "disabled"
                    }>`;
                },
                className: "text-center",
                width: "45px",
            },
            { data: "order_code" },
            {
                data: "order_type",
                render: function (data) {
                    if (!data) return "";
                    return data.replace(/-/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
                },
            },
            {
                data: "total_amount",
                render: (data) => formatCurrency(data),
            },
            {
                data: "status_html",
                className: "text-center",
                render: (data) => data || '<span class="badge bg-secondary">Unknown</span>',
            },
            { data: "reported_by" },
            { data: "reported_at" },
            { data: "reviewed_by" },
            { data: "reviewed_at" },
            {
                data: "remarks",
                render: function (data) {
                    return data ? `<span class="text-muted">${data}</span>` : "";
                },
            },
        ],
        drawCallback: function () {
            $selectAllReports.prop("checked", false);
            updateActionButtons();
        },
    });

    $("#finance-sales-table tbody").on(
        "change",
        ".sales-report-checkbox",
        function () {
            const total = $(".sales-report-checkbox:not(:disabled)").length;
            const checked = $(".sales-report-checkbox:checked").length;
            if (total > 0 && total === checked) {
                $selectAllReports.prop("checked", true);
            } else {
                $selectAllReports.prop("checked", false);
            }
            updateActionButtons();
        }
    );

    $selectAllReports.on("change", function () {
        const isChecked = $(this).is(":checked");
        $(".sales-report-checkbox:not(:disabled)").prop("checked", isChecked);
        updateActionButtons();
    });

    $("#refresh-sales-reports").on("click", function () {
        salesTable.ajax.reload();
        loadAnalytics();
    });

    $statusFilter.on("change", function () {
        salesTable.ajax.reload();
    });

    function processReports(action) {
        const selected = $(".sales-report-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if (selected.length === 0) {
            Toast.fire({
                icon: "warning",
                title: "No reports selected",
                text: "Please select at least one report.",
            });
            return;
        }

        Swal.fire({
            title: action === "approve" ? "Approve reports?" : "Reject reports?",
            text: action === "approve" ? "Are you sure you want to approve?" : "Are you sure you want to reject?",
            icon: "question",
            input: action === "reject" ? "textarea" : "hidden",
            inputPlaceholder: "Provide remarks (optional)",
            inputAttributes: {
                maxlength: 1000,
            },
            inputValue: "",
            showCancelButton: true,
            confirmButtonText: action === "approve" ? "Approve" : "Reject",
            confirmButtonColor: action === "approve" ? "#3085d6" : "#d33",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $("#LoadingScreen").fadeIn(200);
            $.ajax({
                url: "/finance/sales-transactions/review",
                type: "POST",
                data: {
                    report_ids: selected,
                    action: action,
                    remarks: result.value,
                },
                success: function (response) {
                    Toast.fire({
                        icon: "success",
                        title: "Success",
                        text: response.message,
                    });
                    salesTable.ajax.reload();
                    loadAnalytics();
                },
                error: function (xhr) {
                    const message =
                        xhr.responseJSON?.message ||
                        "Failed to process sales reports.";
                    Toast.fire({
                        icon: "error",
                        title: "Error",
                        text: message,
                    });
                },
                complete: function () {
                    $("#LoadingScreen").fadeOut(200);
                },
            });
        });
    }

    $approveBtn.on("click", function () {
        processReports("approve");
    });

    $rejectBtn.on("click", function () {
        processReports("reject");
    });

    function loadAnalytics() {
        $.ajax({
            url: "/finance/sales-transactions/analytics",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (salesChart) {
                    salesChart.updateOptions({
                        xaxis: { categories: response.labels },
                    });
                    salesChart.updateSeries(response.series);
                }
                $pendingCount.text(response.meta.pending);
                $approvedToday.text(formatCurrency(response.meta.approved_today));
            },
            error: function (xhr) {
                console.error(
                    "Failed to load sales analytics:",
                    xhr.responseText
                );
            },
        });
    }

    initChart();
    loadAnalytics();
});

