;(function () {
    var $ = window.jQuery;

    if (!$) {
        console.warn('Executives dashboard requires jQuery.');
        return;
    }

    function formatNumber(value) {
        return Number(value || 0).toLocaleString();
    }

    function formatCurrency(value) {
        var amount = Number(value || 0);
        return '₱ ' + amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function formatRating(value) {
        var num = Number(value);
        if (isNaN(num)) {
            return 'Pending';
        }
        return num.toFixed(2) + ' / 5';
    }

    function isNil(value) {
        return value === null || value === undefined;
    }

    function setText(selector, value, formatter, emptyText) {
        if (emptyText === undefined) {
            emptyText = '—';
        }

        var $el = $(selector);
        if (!$el.length) return;

        if (isNil(value) || value === '') {
            $el.text(emptyText);
            return;
        }

        $el.text(formatter ? formatter(value) : value);
    }

    function setHtmlMessage(selector, message) {
        var $el = $(selector);
        if (!$el.length) return;

        $el
            .empty()
            .append(
                $('<div class="text-muted text-center small py-4"></div>').text(message || 'No data available.')
            );
    }

    function renderList(selector, items, renderItem, emptyText) {
        var $list = $(selector);
        if (!$list.length) return;

        $list.empty();

        if (!items || !items.length) {
            $list.append(
                $('<li class="list-group-item px-0 text-muted"></li>').text(emptyText || 'No data available.')
            );
            return;
        }

        $.each(items, function (_, item) {
            var $item = renderItem(item);
            if ($item) {
                $list.append($item);
            }
        });
    }

    function setErrorState() {
        var errorText = 'Error';
        var fields = [
            '#summary-revenue-today',
            '#summary-budget-released',
            '#summary-pending-approvals',
            '#summary-workforce',
            '#summary-inventory-alerts',
            '#summary-customer-satisfaction',
            '#hr-total-employees',
            '#hr-new-hires',
            '#hr-pending-leaves',
            '#hr-pending-overtime',
            '#ops-sales-today',
            '#ops-completed-orders',
            '#ops-avg-order-value',
            '#ops-orders-in-progress',
            '#finance-payroll-net',
            '#finance-budget-released',
            '#finance-po-spending',
            '#finance-budget-variance',
            '#finance-pending-budgets',
            '#finance-sales-approved',
            '#proc-pending-prs',
            '#proc-open-pos',
            '#proc-overdue-pos',
            '#proc-month-spend',
            '#inventory-to-receive',
            '#inventory-total-skus',
            '#inventory-low-stock',
            '#inventory-out-of-stock',
            '#crm-upcoming-bookings',
            '#crm-pending-bookings',
            '#crm-pending-feedback',
            '#crm-average-rating',
        ];

        $.each(fields, function (_, selector) {
            setText(selector, null, null, errorText);
        });

        renderList('#ops-top-products', [], null, 'Unable to load data.');
        renderList('#proc-top-suppliers', [], null, 'Unable to load data.');
        renderList('#inventory-critical-items', [], null, 'Unable to load data.');
        renderList('#crm-upcoming-list', [], null, 'Unable to load data.');

        setHtmlMessage('#chart-ops-top-products', 'Unable to load chart.');
        setHtmlMessage('#chart-finance-budget-vs-spend', 'Unable to load chart.');
        setHtmlMessage('#chart-proc-top-suppliers', 'Unable to load chart.');
        setHtmlMessage('#chart-inventory-alerts', 'Unable to load chart.');
        setHtmlMessage('#chart-crm-average-rating', 'Unable to load chart.');
    }

    var chartsInstances = {};

    function destroyChart(selector) {
        if (chartsInstances[selector]) {
            chartsInstances[selector].destroy();
            delete chartsInstances[selector];
        }
    }

    function renderChart(selector, options, emptyMessage) {
        var element = document.querySelector(selector);
        if (!element) return;

        destroyChart(selector);

        if (!options) {
            element.innerHTML =
                '<div class="text-muted text-center small py-4">' +
                (emptyMessage || 'No data available.') +
                '</div>';
            return;
        }

        element.innerHTML = '';
        var chart = new window.ApexCharts(element, options);
        chartsInstances[selector] = chart;
        chart.render();
    }

    function hasSeriesData(series) {
        if (!series || !series.length) return false;

        for (var i = 0; i < series.length; i += 1) {
            var data = series[i] && series[i].data;
            if (data && data.length) {
                for (var j = 0; j < data.length; j += 1) {
                    if (Number(data[j]) !== 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function renderCharts(chartData) {
        if (!window.ApexCharts) {
            console.warn('ApexCharts library is not loaded.');
            return;
        }

        var ops = chartData && chartData.operationsTopProducts;
        if (ops && ops.labels && hasSeriesData(ops.series)) {
            renderChart('#chart-ops-top-products', {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: ops.series,
                xaxis: {
                    categories: ops.labels,
                    labels: { style: { fontSize: '12px' } },
                },
                plotOptions: {
                    bar: { horizontal: true, borderRadius: 6, barHeight: '60%' },
                },
                dataLabels: { enabled: true },
                colors: ['#435ebe'],
            });
        } else {
            renderChart('#chart-ops-top-products', null, 'No product data yet.');
        }

        var finance = chartData && chartData.financeBudgetVsSpend;
        if (finance && finance.categories && hasSeriesData(finance.series)) {
            renderChart('#chart-finance-budget-vs-spend', {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: finance.series,
                xaxis: {
                    categories: finance.categories,
                    labels: { style: { fontSize: '12px' } },
                },
                plotOptions: {
                    bar: { borderRadius: 6, columnWidth: '40%' },
                },
                dataLabels: { enabled: false },
                colors: ['#00b8a9'],
            });
        } else {
            renderChart('#chart-finance-budget-vs-spend', null, 'No finance data yet.');
        }

        var procurement = chartData && chartData.procurementTopSuppliers;
        if (procurement && procurement.labels && hasSeriesData(procurement.series)) {
            renderChart('#chart-proc-top-suppliers', {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: procurement.series,
                xaxis: {
                    categories: procurement.labels,
                    labels: { style: { fontSize: '12px' } },
                },
                plotOptions: {
                    bar: { horizontal: true, borderRadius: 6, barHeight: '60%' },
                },
                dataLabels: { enabled: true },
                colors: ['#f9a826'],
            });
        } else {
            renderChart('#chart-proc-top-suppliers', null, 'No supplier data yet.');
        }

        var inventory = chartData && chartData.inventoryAlerts;
        if (inventory && inventory.series && inventory.series.length) {
            var hasInventoryData = false;
            for (var i = 0; i < inventory.series.length; i += 1) {
                if (Number(inventory.series[i]) > 0) {
                    hasInventoryData = true;
                    break;
                }
            }

            if (hasInventoryData) {
                renderChart('#chart-inventory-alerts', {
                    chart: { type: 'donut', height: 260 },
                    series: inventory.series,
                    labels: inventory.labels,
                    dataLabels: { enabled: true },
                    legend: { position: 'bottom' },
                    colors: ['#ff7976', '#d6336c'],
                });
            } else {
                renderChart('#chart-inventory-alerts', null, 'Inventory levels are stable.');
            }
        } else {
            renderChart('#chart-inventory-alerts', null, 'Inventory levels are stable.');
        }

        var crm = chartData && chartData.crmAverageRating;
        if (crm && !isNil(crm.percentage)) {
            var pct = Math.min(Math.max(Number(crm.percentage || 0), 0), 100);
            var ratingValue = Number(crm.value || 0);

            renderChart('#chart-crm-average-rating', {
                chart: { type: 'radialBar', height: 260 },
                series: [pct],
                labels: ['Satisfaction'],
                plotOptions: {
                    radialBar: {
                        hollow: { size: '60%' },
                        dataLabels: {
                            name: { offsetY: -5 },
                            value: {
                                formatter: function () {
                                    return ratingValue ? ratingValue.toFixed(2) : '0.00';
                                },
                                fontSize: '20px',
                            },
                        },
                    },
                },
                colors: ['#435ebe'],
            });
        } else {
            renderChart('#chart-crm-average-rating', null, 'No feedback data yet.');
        }
    }

    function init() {
        var $dashboard = $('#executives-dashboard');
        if (!$dashboard.length) return;

        var endpoint = $dashboard.data('endpoint');
        if (!endpoint) {
            console.warn('Executives analytics endpoint not provided.');
            return;
        }

        $.ajax({
            url: endpoint,
            method: 'GET',
            dataType: 'json',
        })
            .done(function (data) {
                if (!data || typeof data !== 'object') {
                    setErrorState();
                    return;
                }

                var summary = data.summary || {};
                var hr = data.hr || {};
                var operations = data.operations || {};
                var finance = data.finance || {};
                var procurement = data.procurement || {};
                var inventory = data.inventory || {};
                var crm = data.crm || {};
                var charts = data.charts || {};

                setText('#summary-revenue-today', summary.revenueToday, formatCurrency);
                setText('#summary-budget-released', summary.budgetReleased, formatCurrency);
                setText('#summary-pending-approvals', summary.pendingApprovals, formatNumber, '0');
                setText('#summary-workforce', summary.workforceCount, function (val) {
                    return formatNumber(val) + ' Employees';
                });
                setText('#summary-inventory-alerts', summary.inventoryAlerts, formatNumber, '0');
                setText('#summary-customer-satisfaction', summary.customerSatisfaction, formatRating, 'Pending');

                setText('#hr-total-employees', hr.totalEmployees, formatNumber, '0');
                setText('#hr-new-hires', hr.newHires30, formatNumber, '0');
                setText('#hr-pending-leaves', hr.pendingLeaves, formatNumber, '0');
                setText('#hr-pending-overtime', hr.pendingOvertime, formatNumber, '0');

                setText('#ops-sales-today', operations.salesToday, formatCurrency);
                setText('#ops-completed-orders', operations.completedOrders, formatNumber, '0');
                setText('#ops-avg-order-value', operations.avgOrderValue, formatCurrency);
                setText('#ops-orders-in-progress', operations.ordersInProgress, formatNumber, '0');

                renderList(
                    '#ops-top-products',
                    operations.topProducts,
                    function (product) {
                        var total = formatNumber(product.total);

                        return $(
                            '<li class="list-group-item px-0 d-flex justify-content-between align-items-center"></li>'
                        )
                            .append($('<span></span>').text(product.name || 'Unnamed'))
                            .append(
                                $('<span class="badge bg-primary-subtle text-primary"></span>').text(total + ' sold')
                            );
                    },
                    'No completed orders yet.'
                );

                setText('#finance-payroll-net', finance.payrollNet, formatCurrency);
                setText('#finance-budget-released', finance.budgetReleased, formatCurrency);
                setText('#finance-po-spending', finance.poSpending, formatCurrency);
                setText('#finance-budget-variance', finance.budgetVariance, formatCurrency);
                setText('#finance-pending-budgets', finance.pendingBudgets, formatNumber, '0');
                setText('#finance-sales-approved', finance.salesApproved, formatCurrency);

                setText('#proc-pending-prs', procurement.pendingPR, formatNumber, '0');
                setText('#proc-open-pos', procurement.openPO, formatNumber, '0');
                setText('#proc-overdue-pos', procurement.overduePO, formatNumber, '0');
                setText('#proc-month-spend', procurement.monthSpend, formatCurrency);

                renderList(
                    '#proc-top-suppliers',
                    procurement.topSuppliers,
                    function (supplier) {
                        return $(
                            '<li class="list-group-item px-0 d-flex justify-content-between align-items-center"></li>'
                        )
                            .append($('<span></span>').text(supplier.supplier || 'Unnamed Supplier'))
                            .append(
                                $('<span class="badge bg-success-subtle text-success"></span>').text(
                                    formatCurrency(supplier.total)
                                )
                            );
                    },
                    'No supplier spend recorded yet.'
                );

                setText('#inventory-to-receive', inventory.toReceive, formatNumber, '0');
                setText('#inventory-total-skus', inventory.totalSkus, formatNumber, '0');
                setText('#inventory-low-stock', inventory.lowStock, formatNumber, '0');
                setText('#inventory-out-of-stock', inventory.outOfStock, formatNumber, '0');

                renderList(
                    '#inventory-critical-items',
                    inventory.criticalItems,
                    function (item) {
                        var stockText = formatNumber(item.stock) + (item.unit ? ' ' + item.unit : '');

                        return $(
                            '<li class="list-group-item px-0 d-flex justify-content-between align-items-center"></li>'
                        )
                            .append($('<span></span>').text(item.name || 'Unnamed Item'))
                            .append(
                                $('<span class="badge bg-warning-subtle text-warning"></span>').text(stockText)
                            );
                    },
                    'Stock levels are stable.'
                );

                setText('#crm-upcoming-bookings', crm.upcomingBookings, formatNumber, '0');
                setText('#crm-pending-bookings', crm.pendingBookings, formatNumber, '0');
                setText('#crm-pending-feedback', crm.pendingFeedback, formatNumber, '0');
                setText('#crm-average-rating', crm.averageRating, formatRating, 'Pending');

                renderList(
                    '#crm-upcoming-list',
                    crm.upcomingList,
                    function (booking) {
                        var name = booking.name || 'Guest';
                        var date = booking.date || '—';
                        var time = booking.time || '—';
                        var people = formatNumber(booking.people || 0);
                        var table = booking.table || 'TBD';

                        return $('<li class="list-group-item px-0"></li>').append(
                            $('<div class="d-flex justify-content-between"></div>')
                                .append(
                                    $('<div></div>')
                                        .append($('<strong></strong>').text(name))
                                        .append($('<div class="text-muted small"></div>').text(date + ' · ' + time))
                                )
                                .append(
                                    $('<div class="text-end"></div>')
                                        .append(
                                            $('<span class="badge bg-primary-subtle text-primary"></span>').text(
                                                people + ' pax'
                                            )
                                        )
                                        .append($('<div class="text-muted small"></div>').text(table))
                                )
                        );
                    },
                    'No upcoming bookings scheduled.'
                );

                renderCharts(charts);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Failed to load executives analytics:', textStatus, errorThrown);
                setErrorState();
            });
    }

    $(document).ready(init);
})();

