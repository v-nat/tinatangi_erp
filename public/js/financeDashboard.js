$(document).ready(function () {
    if ($('#chart-budget-released').length === 0) {
        return;
    }

    function formatCurrency(value) {
        return '₱ ' + parseFloat(value).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    const optionsBudgetReleased = {
        chart: {
            type: 'line',
            height: 350,
            zoom: { enabled: false },
            toolbar: { show: false }
        },
        series: [{
            name: 'Amount Released',
            data: []
        }],
        xaxis: {
            categories: []
        },
        yaxis: {
            title: {
                text: 'Amount (PHP)'
            },
            labels: {
                formatter: (value) => { return '₱' + value.toLocaleString('en-US') }
            }
        },
        tooltip: {
            y: {
                formatter: (value) => { return formatCurrency(value) }
            }
        },
        stroke: {
            curve: 'smooth'
        },
        noData: {
            text: 'Loading chart data...'
        }
    };

    const optionsPayrollDept = {
        chart: {
            type: 'donut',
            height: 350,
        },
        series: [],
        labels: [],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%"
            },
        },
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: (value) => { return formatCurrency(value) }
            }
        },
        noData: {
            text: 'Loading chart data...'
        }
    };

    const chartBudgetReleased = new ApexCharts(document.querySelector("#chart-budget-released"), optionsBudgetReleased);
    chartBudgetReleased.render();

    const chartPayrollDept = new ApexCharts(document.querySelector("#chart-payroll-dept"), optionsPayrollDept);
    chartPayrollDept.render();

    function loadDashboardData() {
        $.ajax({
            url: '/finance/dashboard-analytics',
            type: 'GET',
            dataType: 'json',
            success: function (response) {

                $('#kpi-pending-payroll').text(formatCurrency(response.kpis.pendingPayroll));
                $('#kpi-pending-budgets').text(formatCurrency(response.kpis.pendingBudgets));
                $('#kpi-pending-pos').text(formatCurrency(response.kpis.pendingPOs));
                $('#kpi-pending-invoices').text(formatCurrency(response.kpis.pendingInvoices));

                const $budgetsTableBody = $('#table-awaiting-budgets tbody');
                $budgetsTableBody.empty();
                if (response.tables.budgetsAwaitingRelease.length > 0) {
                    $.each(response.tables.budgetsAwaitingRelease, function (index, item) {
                        const row = `
                            <tr>
                                <td>${item.requestor}</td>
                                <td>${item.department}</td>
                                <td>${formatCurrency(item.amount)}</td>
                            </tr>
                        `;
                        $budgetsTableBody.append(row);
                    });
                } else {
                    $budgetsTableBody.append('<tr><td colspan="3" class="text-center">No budgets awaiting release.</td></tr>');
                }

                const $invoicesTableBody = $('#table-awaiting-invoices tbody');
                $invoicesTableBody.empty();
                if (response.tables.invoicesAwaitingApproval.length > 0) {
                    $.each(response.tables.invoicesAwaitingApproval, function (index, item) {
                        const row = `
                            <tr>
                                <td>${item.supplier}</td>
                                <td>${item.po_id}</td>
                                <td>${formatCurrency(item.total_amount)}</td>
                            </tr>
                        `;
                        $invoicesTableBody.append(row);
                    });
                } else {
                    $invoicesTableBody.append('<tr><td colspan="3" class="text-center">No invoices awaiting approval.</td></tr>');
                }

                chartBudgetReleased.updateSeries([{ data: response.charts.budgetReleased.data }]);
                chartBudgetReleased.updateOptions({ xaxis: { categories: response.charts.budgetReleased.labels } });

                if (response.charts.payrollByDept.data.length > 0 && response.charts.payrollByDept.data.some(d => d > 0)) {
                    chartPayrollDept.updateOptions({
                        labels: response.charts.payrollByDept.labels,
                        series: response.charts.payrollByDept.data
                    });
                } else {
                    chartPayrollDept.updateOptions({
                        series: [],
                        labels: [],
                        noData: { text: 'No payroll data available.' }
                    });
                }

            },
            error: function (xhr) {
                console.error('Failed to load dashboard analytics:', xhr);
                chartBudgetReleased.updateOptions({ noData: { text: 'Could not load chart data.' } });
                chartPayrollDept.updateOptions({ noData: { text: 'Could not load chart data.' } });
            }
        });
    }

    loadDashboardData();
});
