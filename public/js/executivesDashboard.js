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
            '#ops-voided-orders',
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
            '#crm-today-active',
            '#crm-pending-feedback',
            '#crm-average-rating',
            '#exec-best-sellers-weekly-range',
            '#exec-best-sellers-monthly-range',
            '#exec-forecast-range',
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
        setHtmlMessage('#chart-exec-best-sellers-weekly', 'Unable to load chart.');
        setHtmlMessage('#chart-exec-best-sellers-monthly', 'Unable to load chart.');
        setHtmlMessage('#chart-exec-best-sellers-forecast', 'Unable to load chart.');

        var $forecastContext = $('#exec-forecast-context');
        if ($forecastContext.length) {
            $forecastContext.text('Unable to load forecast.');
        }
    }

    var chartsInstances = {};
    var bestSellerChartData = null;
    var activeBestSellerMode = 'weekly';

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

    function updateBestSellerToggleButtons() {
        var $buttons = $('[data-best-seller-toggle]');
        $buttons.each(function () {
            var $button = $(this);
            var mode = $button.data('best-seller-toggle');
            var hasData =
                bestSellerChartData &&
                bestSellerChartData[mode] &&
                hasSeriesData(bestSellerChartData[mode].series);

            $button.toggleClass('active', mode === activeBestSellerMode);
            $button.prop('disabled', !hasData);
        });
    }

    function renderActiveBestSellerChart() {
        var chartSelector = '#chart-exec-best-sellers';
        var $range = $('#exec-best-sellers-range');

        if (!bestSellerChartData) {
            renderChart(chartSelector, null, 'No best seller data yet.');
            if ($range.length) {
                $range.text('No data yet');
            }
            updateBestSellerToggleButtons();
            return;
        }

        var chartInfo = bestSellerChartData[activeBestSellerMode];

        if (!chartInfo) {
            renderChart(chartSelector, null, 'No best seller data yet.');
            if ($range.length) {
                $range.text('No data for this period');
            }
            updateBestSellerToggleButtons();
            return;
        }

        if ($range.length) {
            if (chartInfo.label) {
                $range.text(chartInfo.label);
            } else {
                $range.text('No data yet');
            }
        }

        if (chartInfo.labels && hasSeriesData(chartInfo.series)) {
            renderChart(chartSelector, {
                chart: { type: 'line', height: 300, toolbar: { show: false } },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                },
                markers: {
                    size: 4,
                    strokeWidth: 2,
                },
                dataLabels: { enabled: false },
                series: chartInfo.series,
                xaxis: {
                    categories: chartInfo.labels,
                    labels: { style: { fontSize: '12px' } },
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return formatCurrency(val);
                        },
                    },
                    title: {
                        text: 'Total Revenue (₱)',
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return formatCurrency(val);
                        },
                    },
                },
                legend: {
                    position: 'top',
                },
                colors: ['#435ebe', '#5c7cfa', '#69db7c', '#ffa94d', '#ff6b6b', '#5c940d'],
            });
        } else {
            renderChart(chartSelector, null, 'No best seller data yet.');
        }

        updateBestSellerToggleButtons();
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

        var bestSellers = chartData && chartData.bestSellers;
        bestSellerChartData = bestSellers || null;

        if (
            bestSellerChartData &&
            !bestSellerChartData[activeBestSellerMode]
        ) {
            var fallbackOrder = ['weekly', 'monthly', 'sixMonths', 'yearly'];
            for (var i = 0; i < fallbackOrder.length; i += 1) {
                var candidate = fallbackOrder[i];
                if (
                    bestSellerChartData[candidate] &&
                    hasSeriesData(bestSellerChartData[candidate].series)
                ) {
                    activeBestSellerMode = candidate;
                    break;
                }
            }

            if (!bestSellerChartData[activeBestSellerMode]) {
                for (var j = 0; j < fallbackOrder.length; j += 1) {
                    var fallbackCandidate = fallbackOrder[j];
                    if (bestSellerChartData[fallbackCandidate]) {
                        activeBestSellerMode = fallbackCandidate;
                        break;
                    }
                }
            }
        }

        renderActiveBestSellerChart();

        var bestSellerForecast = chartData && chartData.bestSellerForecast;
        var $forecastRange = $('#exec-forecast-range');
        var $forecastContext = $('#exec-forecast-context');
        var defaultForecastContext = 'Projected revenue for the next 7 days based on current best sellers.';

        if ($forecastRange.length) {
            var forecastRangeText =
                (bestSellerForecast && (bestSellerForecast.upcoming_range || bestSellerForecast.label)) ||
                'No data yet';
            $forecastRange.text(forecastRangeText);
        }

        if ($forecastContext.length) {
            if (
                bestSellerForecast &&
                bestSellerForecast.metadata &&
                typeof bestSellerForecast.metadata.averageDailyRevenue !== 'undefined'
            ) {
                $forecastContext.text(
                    'Based on ' +
                        formatCurrency(bestSellerForecast.metadata.averageDailyRevenue) +
                        " average daily revenue from this week's best sellers."
                );
            } else {
                $forecastContext.text(defaultForecastContext);
            }
        }

        if (
            bestSellerForecast &&
            bestSellerForecast.labels &&
            hasSeriesData(bestSellerForecast.series)
        ) {
            var dashArray = bestSellerForecast.dashArray || [];
            var seriesLength = bestSellerForecast.series.length;
            if (dashArray.length < seriesLength) {
                var diff = seriesLength - dashArray.length;
                for (var da = 0; da < diff; da += 1) {
                    dashArray.push(0);
                }
            }

            renderChart('#chart-exec-best-sellers-forecast', {
                chart: { type: 'line', height: 300, toolbar: { show: false } },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    dashArray: dashArray,
                },
                markers: {
                    size: 4,
                    strokeWidth: 2,
                },
                dataLabels: { enabled: false },
                series: bestSellerForecast.series,
                xaxis: {
                    categories: bestSellerForecast.labels,
                    labels: {
                        rotate: -45,
                        style: { fontSize: '11px' },
                    },
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return formatCurrency(val);
                        },
                    },
                    title: {
                        text: 'Projected Revenue (₱)',
                    },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return formatCurrency(val);
                        },
                    },
                },
                legend: {
                    position: 'top',
                },
                colors: ['#00b8a9', '#868e96', '#5f3dc4'],
            });
        } else {
            renderChart(
                '#chart-exec-best-sellers-forecast',
                null,
                'Not enough data to generate a forecast.'
            );
        }

        var crm = chartData && chartData.crmAverageRating;
        if (crm && !isNil(crm.percentage)) {
            var pct = Math.min(Math.max(Number(crm.percentage || 0), 0), 100);
            var ratingValue = Number(crm.value || 0);

            renderChart('#chart-crm-average-rating', (function () {
                var docStyle = getComputedStyle(document.documentElement);
                var theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
                var bodyColor = docStyle.getPropertyValue('--bs-body-color') || '#6c757d';
                var trackColor = docStyle.getPropertyValue('--bs-border-color') || (theme === 'dark' ? '#3a3f44' : '#e9ecef');
                var seriesColor = theme === 'dark' ? '#5c7cfa' : '#435ebe';

                return {
                    chart: {
                        type: 'radialBar',
                        height: 260,
                        foreColor: bodyColor.trim(),
                        toolbar: { show: false },
                    },
                    series: [pct],
                    labels: ['Satisfaction'],
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '60%',
                                background: theme === 'dark' ? 'rgba(255,255,255,0.04)' : '#f8f9fa',
                            },
                            track: {
                                background: trackColor.trim(),
                                strokeWidth: '100%',
                                opacity: theme === 'dark' ? 0.4 : 1,
                            },
                            dataLabels: {
                                name: {
                                    offsetY: -5,
                                },
                                value: {
                                    formatter: function () {
                                        return ratingValue ? ratingValue.toFixed(2) : '0.00';
                                    },
                                    fontSize: '20px',
                                },
                            },
                        },
                    },
                    colors: [seriesColor],
                    fill: {
                        type: 'solid',
                        colors: [seriesColor],
                    },
                    stroke: {
                        lineCap: 'round',
                    },
                    grid: {
                        borderColor: 'rgba(0,0,0,0)',
                    },
                };
            })());
        } else {
            renderChart('#chart-crm-average-rating', null, 'No feedback data yet.');
        }
    }

    function init() {
        var $dashboard = $('#executives-dashboard');
        if (!$dashboard.length) return;

        $(document).on('click', '[data-best-seller-toggle]', function (event) {
            event.preventDefault();
            var mode = $(this).data('best-seller-toggle');
            if (!mode || mode === activeBestSellerMode) {
                return;
            }
            activeBestSellerMode = mode;
            renderActiveBestSellerChart();
        });

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
                setText('#ops-voided-orders', operations.voidedOrders, formatNumber, '0');

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
                setText('#crm-today-active', crm.todayActiveBookings, formatNumber, '0');
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
                        var tableNum = booking.table_number ? ' #' + booking.table_number : '';
                        var duration = booking.duration_hours ? booking.duration_hours + 'h' : '';
                        var tableLabel = table + tableNum + (duration ? ' · ' + duration : '');

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
                                        .append($('<div class="text-muted small"></div>').text(tableLabel))
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

