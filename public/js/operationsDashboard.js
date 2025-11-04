$(document).ready(function () {
    if ($("#chart-top-products").length === 0) {
        return;
    }

    const getThemeColor = (colorName) => {
        return getComputedStyle(document.documentElement)
            .getPropertyValue(`--bs-${colorName}`)
            .trim();
    };

    const formatCurrency = (val) => {
        if (val === null || val === undefined) return "₱0.00";
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

    let topProductsChart;

    function initCharts() {
        const topProductsOptions = {
            series: [],
            chart: {
                type: "bar",
                height: 350,
                toolbar: { show: false },
                noData: { text: "Loading chart data..." },
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '50%',
                    distributed: true,
                },
            },
            dataLabels: { enabled: true, formatter: (val) => formatNumber(val) + ' sold' },
            xaxis: {
                categories: [],
                title: { text: "Units Sold" }
            },
            yaxis: {
                title: { text: "Products" }
            },
            legend: { show: false },
            colors: [
                getThemeColor("primary"),
                getThemeColor("success"),
                getThemeColor("warning"),
                getThemeColor("danger"),
                getThemeColor("info"),
            ],
        };

        topProductsChart = new ApexCharts(
            document.querySelector("#chart-top-products"),
            topProductsOptions
        );
        topProductsChart.render();
    }

    function loadDashboardData() {
        $.ajax({
            url: "/operations/dashboard-analytics",
            type: "GET",
            dataType: "json",
            success: function (response) {

                $("#kpi-sales-today").text(formatCurrency(response.kpis.salesToday));
                $("#kpi-orders-today").text(formatNumber(response.kpis.ordersToday));
                $("#kpi-avg-value").text(formatCurrency(response.kpis.avgOrderValue));
                $("#kpi-pending-orders").text(formatNumber(response.kpis.pendingOrders));

                if (topProductsChart) {
                    topProductsChart.updateOptions({
                        xaxis: {
                            categories: response.charts.topProducts.labels,
                        },
                    });
                    topProductsChart.updateSeries([
                        { name: "Total Sold", data: response.charts.topProducts.series },
                    ]);
                }

                $("#status-in-queue").text(formatNumber(response.tables.liveStatus.in_queue));
                $("#status-in-prep").text(formatNumber(response.tables.liveStatus.in_prep));
                $("#status-ready").text(formatNumber(response.tables.liveStatus.ready));

                const $lowStockBody = $("#table-low-stock tbody");
                $lowStockBody.empty();
                if (response.tables.lowStockItems && response.tables.lowStockItems.length > 0) {
                    $.each(response.tables.lowStockItems, function (index, item) {
                        const row = `
                            <tr>
                                <td>${item.name}</td>
                                <td>${formatNumber(item.stock_level)}</td>
                                <td>${item.status_html}</td>
                            </tr>
                        `;
                        $lowStockBody.append(row);
                    });
                } else {
                    $lowStockBody.append(
                        '<tr><td colspan="3" class="text-center">All items are well-stocked!</td></tr>'
                    );
                }
            },
            error: function (xhr) {
                console.error("Failed to load dashboard analytics:", xhr.responseText);
                $("#table-low-stock tbody").html(
                    '<tr><td colspan="3" class="text-center text-danger">Failed to load data.</td></tr>'
                );
            },
        });
    }

    initCharts();
    loadDashboardData();

    // Refresh data every 30 seconds
    setInterval(loadDashboardData, 30000);
});
