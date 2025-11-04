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

    let budgetSpendingChart, payrollOverviewChart, spendingDepartmentChart;

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
                $("#kpi-total-payroll").text(
                    formatCurrency(response.kpis.totalPayroll)
                );
                $("#kpi-budget-released").text(
                    formatCurrency(response.kpis.budgetReleased)
                );
                $("#kpi-po-spending").text(
                    formatCurrency(response.kpis.poSpending)
                );
                $("#kpi-pending-prs").text(
                    formatNumber(response.kpis.pendingPRs)
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
                $("#kpi-total-payroll").text("Error");
                $("#kpi-budget-released").text("Error");
                $("#kpi-po-spending").text("Error");
                $("#kpi-pending-prs").text("Error");
                $("#table-pending-budgets tbody").html(
                    '<tr><td colspan="2" class="text-center text-danger">Failed to load data.</td></tr>'
                );
            },
        });
    }

    initCharts();

    loadDashboardData();
});

