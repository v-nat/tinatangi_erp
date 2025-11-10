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
        if (val === null || val === undefined || isNaN(val)) return "₱0.00";
        return (
            "₱" +
            parseFloat(val).toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            })
        );
    };

    const formatNumber = (val) => {
        if (val === null || val === undefined || isNaN(val)) return "0";
        return parseFloat(val).toLocaleString("en-US");
    };

    const formatPercent = (value, total) => {
        if (!total || total <= 0 || value === null || value === undefined) {
            return "0%";
        }
        const percent = (Number(value) / Number(total)) * 100;
        return `${percent.toFixed(1)}%`;
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
                const kpis = response.kpis || {};
                const charts = response.charts || {};
                const tables = response.tables || {};

                $("#kpi-sales-today").text(formatCurrency(kpis.salesToday));
                $("#kpi-total-orders").text(formatNumber(kpis.totalOrders));
                $("#kpi-completed-orders").text(formatNumber(kpis.completedOrders));
                $("#kpi-avg-value").text(formatCurrency(kpis.avgOrderValue));
                $("#kpi-in-progress").text(formatNumber(kpis.inProgressOrders));
                $("#kpi-voided-orders").text(formatNumber(kpis.voidedOrders));

                const topProducts = charts.topProducts || { labels: [], series: [] };
                if (topProductsChart) {
                    topProductsChart.updateOptions({
                        xaxis: {
                            categories: topProducts.labels || [],
                        },
                    });
                    topProductsChart.updateSeries([
                        {
                            name: "Total Sold",
                            data: topProducts.series || [],
                        },
                    ]);
                }

                const $orderTypeList = $("#order-type-list");
                $orderTypeList.empty();
                const orderTypes = charts.orderTypes || { labels: [], series: [] };
                if (
                    orderTypes.labels &&
                    orderTypes.labels.length > 0 &&
                    orderTypes.series
                ) {
                    orderTypes.labels.forEach(function (label, index) {
                        const count = orderTypes.series[index] || 0;
                        $orderTypeList.append(
                            `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-capitalize">${label}</span>
                                <span>
                                    <span class="badge bg-primary me-2">${formatNumber(count)}</span>
                                    <small class="text-muted">${formatPercent(count, kpis.totalOrders)}</small>
                                </span>
                             </li>`
                        );
                    });
                } else {
                    $orderTypeList.append(
                        '<li class="list-group-item text-center text-muted">No orders placed today.</li>'
                    );
                }

                $("#status-in-queue").text(formatNumber(tables.liveStatus?.in_queue));
                $("#status-in-prep").text(formatNumber(tables.liveStatus?.in_prep));
                $("#status-ready").text(formatNumber(tables.liveStatus?.ready));

                const $lowStockBody = $("#table-low-stock tbody");
                $lowStockBody.empty();
                if (tables.lowStockItems && tables.lowStockItems.length > 0) {
                    $.each(tables.lowStockItems, function (index, item) {
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
