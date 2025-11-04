$(document).ready(function () {

    // Check if we are on the dashboard page
    if ($('#chart-po-status').length === 0) {
        return; // Stop execution if charts aren't present
    }

    // Helper to format currency
    function formatCurrency(value) {
        return '₱ ' + parseFloat(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Helper to format dates
    function formatSimpleDate(dateString) {
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    // --- Chart Options ---

    // 1. Doughnut Chart: PO by Status
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
            data: [] // To be populated
        }],
        xaxis: {
            categories: [] // To be populated
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

    // --- Initialize Charts ---
    const chartPoStatus = new ApexCharts(document.querySelector("#chart-po-status"), optionsPoStatus);
    chartPoStatus.render();

    const chartTopSuppliers = new ApexCharts(document.querySelector("#chart-top-suppliers"), optionsTopSuppliers);
    chartTopSuppliers.render();


    // --- Fetch and Populate Data ---
    function loadDashboardData() {
        $.ajax({
            url: '/procurement/dashboard-analytics',
            type: 'GET',
            dataType: 'json',
            success: function (response) {

                // 1. Populate KPI Cards
                $('#kpi-pending-pr').text(response.kpis.pendingPR);
                $('#kpi-pending-po').text(response.kpis.pendingPO);
                $('#kpi-active-suppliers').text(response.kpis.activeSuppliers);
                // Use the pre-formatted string from the controller
                $('#kpi-total-spend').text('₱ ' + response.kpis.totalSpend);

                // 2. Populate Recent Pending PRs Table
                const $prTableBody = $('#table-recent-prs tbody');
                $prTableBody.empty();
                if (response.recentPendingPRs.length > 0) {
                    $.each(response.recentPendingPRs, function (index, item) {
                        const row = `
                            <tr>
                                <td>PR-${item.id}</td>
                                <td>${formatSimpleDate(item.created_at)}</td>
                                <td>${item.status_html}</td>
                            </tr>
                        `;
                        $prTableBody.append(row);
                    });
                } else {
                    $prTableBody.append('<tr><td colspan="3" class="text-center">No pending purchase requests!</td></tr>');
                }

                // 3. Update PO Status Chart
                const poStatusLabels = response.poByStatus.map(item => item.status_label);
                const poStatusCounts = response.poByStatus.map(item => item.count);
                chartPoStatus.updateOptions({
                    labels: poStatusLabels,
                    series: poStatusCounts
                });

                // 4. Update Top Suppliers Chart
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

    // Load data on page load
    loadDashboardData();
});

