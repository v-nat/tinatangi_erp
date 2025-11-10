$(document).ready(function () {
    if ($("#chart-budget-spending").length === 0) {
        return;
    }

    const getThemeColor = (colorName) => {
        return getComputedStyle(document.documentElement)
            .getPropertyValue(`--bs-${colorName}`)
            .trim();
    };

    const formatCurrency = (val) => {
        if (val === null || val === undefined) return "₱0";
        return (
            "₱" +
            parseFloat(val).toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    };

    const formatNumber = (val) => {
        if (val === null || val === undefined) return "0";
        return parseFloat(val).toLocaleString("en-US");
    };

    const formatCurrencySigned = (val) => {
        const numeric = Number(val) || 0;
        const formatted = formatCurrency(Math.abs(numeric));
        if (numeric > 0) return "+ " + formatted;
        if (numeric < 0) return "- " + formatted;
        return formatted;
    };

    let budgetSpendingChart, payrollOverviewChart, spendingDepartmentChart, salesActivityChart;

    function initCharts() {
        const budgetSpendingChartEl = document.getElementById(
            "chart-budget-spending"
        );
        if (budgetSpendingChartEl) {
            const budgetSpendingOptions = {
                series: [],
                chart: {
                    height: 350,
                    type: "area",
                    toolbar: { show: false },
                    noData: { text: "Loading chart data..." },
                },
                colors: [getThemeColor("primary"), getThemeColor("danger")],
                dataLabels: { enabled: false },
                stroke: { curve: "smooth", width: 2 },
                grid: { strokeDashArray: 5 },
                xaxis: {
                    categories: [],
                },
                yaxis: {
                    title: { text: "Amount (PHP)" },
                    labels: {
                        formatter: (val) => (val / 1000).toFixed(0) + "k",
                    },
                },
                tooltip: {
                    y: { formatter: (val) => formatCurrency(val) },
                },
                legend: { position: "top", horizontalAlign: "right" },
            };
            budgetSpendingChart = new ApexCharts(
                budgetSpendingChartEl,
                budgetSpendingOptions
            );
            budgetSpendingChart.render();
        }

        const payrollOverviewChartEl = document.getElementById(
            "chart-payroll-overview"
        );
        if (payrollOverviewChartEl) {
            const payrollOverviewOptions = {
                series: [],
                chart: {
                    type: "bar",
                    height: 350,
                    toolbar: { show: false },
                    noData: { text: "Loading chart data..." },
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: "55%",
                        endingShape: "rounded",
                    },
                },
                dataLabels: { enabled: false },
                colors: [
                    getThemeColor("primary"),
                    getThemeColor("info"),
                    getThemeColor("success"),
                ],
                stroke: { show: true, width: 2, colors: ["transparent"] },
                xaxis: {
                    categories: [],
                },
                yaxis: {
                    title: { text: "Amount (PHP)" },
                    labels: {
                        formatter: (val) => (val / 1000).toFixed(0) + "k",
                    },
                },
                fill: { opacity: 1 },
                tooltip: {
                    y: { formatter: (val) => formatCurrency(val) },
                },
                legend: { position: "top", horizontalAlign: "right" },
            };
            payrollOverviewChart = new ApexCharts(
                payrollOverviewChartEl,
                payrollOverviewOptions
            );
            payrollOverviewChart.render();
        }

        const spendingDepartmentChartEl = document.getElementById(
            "chart-spending-department"
        );
        if (spendingDepartmentChartEl) {
            const spendingDepartmentOptions = {
                series: [],
                chart: {
                    type: "donut",
                    height: 380,
                    noData: { text: "Loading chart data..." },
                },
                labels: [],
                colors: [
                    getThemeColor("primary"),
                    getThemeColor("success"),
                    getThemeColor("warning"),
                    getThemeColor("danger"),
                    getThemeColor("info"),
                ],
                legend: { position: "bottom" },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: { show: true },
                                value: {
                                    show: true,
                                    formatter: (val) => formatCurrency(val),
                                },
                                total: {
                                    show: true,
                                    label: "Total Spending",
                                    formatter: (w) => {
                                        const total =
                                            w.globals.seriesTotals.reduce(
                                                (a, b) => a + b,
                                                0
                                            );
                                        return formatCurrency(total);
                                    },
                                },
                            },
                        },
                    },
                },
            };
            spendingDepartmentChart = new ApexCharts(
                spendingDepartmentChartEl,
                spendingDepartmentOptions
            );
            spendingDepartmentChart.render();
        }

        const salesActivityChartEl = document.getElementById(
            "chart-sales-activity"
        );
        if (salesActivityChartEl) {
            const salesActivityOptions = {
                series: [],
                chart: {
                    type: "line",
                    height: 320,
                    toolbar: { show: false },
                    noData: { text: "Loading chart data..." },
                },
                stroke: { curve: "smooth", width: 3 },
                colors: [getThemeColor("primary"), getThemeColor("success")],
                dataLabels: { enabled: false },
                xaxis: {
                    categories: [],
                    title: { text: "Date" },
                },
                yaxis: {
                    labels: {
                        formatter: (val) => "₱ " + (val / 1000).toFixed(1) + "k",
                    },
                },
                tooltip: {
                    y: { formatter: (val) => formatCurrency(val) },
                },
                legend: { position: "top", horizontalAlign: "right" },
            };
            salesActivityChart = new ApexCharts(
                salesActivityChartEl,
                salesActivityOptions
            );
            salesActivityChart.render();
        }
    }

    function loadDashboardData() {
        if (typeof jQuery === "undefined") {
            console.error("jQuery is not loaded. Make sure to include it.");
            return;
        }

        $.ajax({
            url: "/finance/dashboard-analytics",
            type: "GET",
            dataType: "json",
            success: function (response) {
                $("#kpi-sales-approved").text(
                    formatCurrency(response.kpis.salesApproved)
                );
                $("#kpi-budget-released").text(
                    formatCurrency(response.kpis.budgetReleased)
                );
                $("#kpi-po-spending").text(
                    formatCurrency(response.kpis.poSpending)
                );
                $("#kpi-total-payroll").text(
                    formatCurrency(response.kpis.totalPayroll)
                );
                $("#kpi-sales-pending").text(
                    formatNumber(response.kpis.salesPending)
                );
                $("#kpi-pending-prs").text(
                    formatNumber(response.kpis.pendingPRs)
                );

                const varianceValue = response.kpis.budgetVariance || 0;
                const $variance = $("#kpi-budget-variance");
                $variance
                    .text(formatCurrencySigned(varianceValue))
                    .removeClass("text-success text-danger")
                    .addClass(
                        varianceValue >= 0 ? "text-success" : "text-danger"
                    );
                $("#kpi-budget-variance-note").text(
                    varianceValue >= 0
                        ? "Under budget (released exceeds spending)"
                        : "Over budget (spending exceeds released)"
                );

                $("#kpi-sales-reported-today").text(
                    formatCurrency(response.kpis.salesReportedToday)
                );
                $("#kpi-sales-approved-today").text(
                    formatCurrency(response.kpis.salesApprovedToday)
                );

                if (budgetSpendingChart) {
                    budgetSpendingChart.updateOptions({
                        xaxis: {
                            categories: response.charts.budgetVsSpending.labels,
                        },
                    });
                    budgetSpendingChart.updateSeries(
                        response.charts.budgetVsSpending.series
                    );
                }

                if (payrollOverviewChart) {
                    payrollOverviewChart.updateOptions({
                        xaxis: {
                            categories: response.charts.payrollOverview.labels,
                        },
                    });
                    payrollOverviewChart.updateSeries(
                        response.charts.payrollOverview.series
                    );
                }

                if (spendingDepartmentChart) {
                    spendingDepartmentChart.updateOptions({
                        labels: response.charts.spendingByDepartment.labels,
                    });
                    spendingDepartmentChart.updateSeries(
                        response.charts.spendingByDepartment.series
                    );
                }

                if (salesActivityChart) {
                    const salesActivity =
                        response.charts.salesActivity || {
                            labels: [],
                            series: [],
                        };
                    salesActivityChart.updateOptions({
                        xaxis: {
                            categories: salesActivity.labels,
                        },
                    });
                    salesActivityChart.updateSeries(
                        salesActivity.series
                    );
                }

                const $pendingBudgetsBody = $("#table-pending-budgets tbody");
                $pendingBudgetsBody.empty();
                if (
                    response.tables.pendingBudgets &&
                    response.tables.pendingBudgets.length > 0
                ) {
                    $.each(
                        response.tables.pendingBudgets,
                        function (index, item) {
                            const row = `
                        <tr>
                            <td class="col-auto"><p class="font-bold mb-0">${
                                item.type
                            } (${item.department})</p></td>
                            <td class="col-auto"><p class="mb-0">${formatCurrency(
                                item.amount
                            )}</p></td>
                        </tr>
                    `;
                            $pendingBudgetsBody.append(row);
                        }
                    );
                } else {
                    $pendingBudgetsBody.append(
                        '<tr><td colspan="2" class="text-center">No pending budget requests.</td></tr>'
                    );
                }
            },
            error: function (xhr) {
                console.error(
                    "Failed to load dashboard analytics:",
                    xhr.responseText
                );
                $("#kpi-sales-approved").text("Error");
                $("#kpi-budget-released").text("Error");
                $("#kpi-po-spending").text("Error");
                $("#kpi-total-payroll").text("Error");
                $("#kpi-pending-prs").text("Error");
                $("#kpi-sales-pending").text("Error");
                $("#kpi-budget-variance")
                    .text("Error")
                    .removeClass("text-success text-danger");
                $("#kpi-budget-variance-note").text("Released - Spending");
                $("#kpi-sales-reported-today").text("Error");
                $("#kpi-sales-approved-today").text("Error");
                $("#table-pending-budgets tbody").html(
                    '<tr><td colspan="2" class="text-center text-danger">Failed to load data.</td></tr>'
                );
            },
        });
    }

    initCharts();

    loadDashboardData();
});

