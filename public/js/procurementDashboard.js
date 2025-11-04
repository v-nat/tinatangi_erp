$(document).ready(function () {
    if ($('#chart-po-status').length === 0) {
        return;
    }

    function formatCurrency(value) {
        return '₱ ' + parseFloat(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatSimpleDate(dateString) {
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    const optionsPoStatus = {
        chart: {
            type: 'donut',
            height: 350,
        },
        series: [], // To be populated
        labels: [], // To be populated
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%"
            },
        },
        legend: {
            position: 'bottom'
        },
        noData: {
            text: 'Loading chart data...'
        }
    };

    // 2. Bar Chart: Top Suppliers
    const optionsTopSuppliers = {
        chart: {
            type: 'bar',
            height: 350
        },
        series: [{
            name: 'Total Spend',
            data: []
        }],
        xaxis: {
            categories: []
        },
        yaxis: {
            title: {
                text: 'Total Spend (PHP)'
            },
            labels: {
                formatter: (value) => { return value.toLocaleString('en-US') }
            }
        },
        tooltip: {
            y: {
                formatter: (value) => { return formatCurrency(value) }
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                distributed: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        noData: {
            text: 'Loading chart data...'
        }
    };

    const chartPoStatus = new ApexCharts(document.querySelector("#chart-po-status"), optionsPoStatus);
    chartPoStatus.render();

    const chartTopSuppliers = new ApexCharts(document.querySelector("#chart-top-suppliers"), optionsTopSuppliers);
    chartTopSuppliers.render();

    function loadDashboardData() {
        $.ajax({
            url: '/procurement/dashboard-analytics',
            type: 'GET',
            dataType: 'json',
            success: function (response) {

                $('#kpi-pending-pr').text(response.kpis.pendingPR);
                $('#kpi-pending-po').text(response.kpis.pendingPO);
                $('#kpi-active-suppliers').text(response.kpis.activeSuppliers);
                $('#kpi-total-spend').text(formatCurrency(response.kpis.totalSpend));

                const $prTableBody = $('#table-recent-prs tbody');
                $prTableBody.empty();
                if (response.recentPendingPRs.length > 0) {
                    $.each(response.recentPendingPRs, function (index, item) {
                        const row = `
                            <tr>
                                <td>${item.order_no || 'N/A'}</td>
                                <td>${formatSimpleDate(item.created_at)}</td>
                                <td>${item.status_html}</td>
                            </tr>
                        `;
                        $prTableBody.append(row);
                    });
                } else {
                    $prTableBody.append('<tr><td colspan="3" class="text-center">No pending purchase requests!</td></tr>');
                }

                const poStatusLabels = response.poByStatus.map(item => item.status_label);
                const poStatusCounts = response.poByStatus.map(item => item.count);
                chartPoStatus.updateOptions({
                    labels: poStatusLabels,
                    series: poStatusCounts
                });

                const supplierLabels = response.topSuppliers.map(item => item.supplier_name);
                const supplierTotals = response.topSuppliers.map(item => item.total);
                chartTopSuppliers.updateSeries([{ data: supplierTotals }]);
                chartTopSuppliers.updateOptions({ xaxis: { categories: supplierLabels } });

            },
            error: function (xhr) {
                console.error('Failed to load dashboard analytics:', xhr);
            }
        });
    }

    loadDashboardData();
});

