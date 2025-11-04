$(document).ready(function () {

    function formatSimpleDate(dateString) {
        const options = { month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    const optionsFeedbackTrend = {
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        series: [{
            name: 'Feedback',
            data: []
        }],
        xaxis: {
            type: 'category',
            categories: []
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            },
        },
        noData: {
            text: 'Loading chart data...'
        }
    };

    const optionsCategoryRatings = {
        chart: {
            type: 'radar',
            height: 350
        },
        series: [{
            name: 'Average Rating',
            data: [],
        }],
        xaxis: {
            categories: ['Food', 'Staff', 'Environment']
        },
        yaxis: {
            min: 0,
            max: 5,
            tickAmount: 5,
            labels: {
                formatter: function (val) {
                    return val.toFixed(0);
                }
            }
        },
        markers: {
            size: 4,
        },
        noData: {
            text: 'Loading chart data...'
        }
    };

    const optionsRatingsDistribution = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                distributed: true,
                horizontal: false,
                columnWidth: '50%',
            },
        },
        dataLabels: { enabled: false },
        series: [{
            name: 'Count',
            data: []
        }],
        xaxis: {
            categories: [],
        },
        legend: { show: false },
        noData: {
            text: 'Loading chart data...'
        }
    };

    const chartFeedbackTrend = new ApexCharts(document.querySelector("#chart-feedback-trend"), optionsFeedbackTrend);
    chartFeedbackTrend.render();

    const chartCategoryRatings = new ApexCharts(document.querySelector("#chart-category-ratings"), optionsCategoryRatings);
    chartCategoryRatings.render();

    const chartRatingsDistribution = new ApexCharts(document.querySelector("#chart-ratings-distribution"), optionsRatingsDistribution);
    chartRatingsDistribution.render();

    function loadDashboardData() {
        $.ajax({
            url: '/customer-service/dashboard-analytics',
            type: 'GET',
            dataType: 'json',
            success: function (response) {

                $('#kpi-avg-rating').text(response.kpis.averageRating + ' ★');
                $('#kpi-total-feedback').text(response.kpis.totalFeedback);
                $('#kpi-displayed-count').text(response.kpis.displayedCount);
                $('#kpi-pending-count').text(response.kpis.pendingCount);

                const $pendingTableBody = $('#table-recent-pending tbody');
                $pendingTableBody.empty();
                if (response.recentPending.length > 0) {
                    $.each(response.recentPending, function (index, item) {
                        const message = item.message.length > 50 ? item.message.substring(0, 50) + '...' : item.message;
                        const row = `
                            <tr>
                                <td>${item.name}</td>
                                <td><em>"${message}"</em></td>
                                <td>${formatSimpleDate(item.created_at)}</td>
                                <td>${item.status_html}</td>
                            </tr>
                        `;
                        $pendingTableBody.append(row);
                    });
                } else {
                    $pendingTableBody.append('<tr><td colspan="4" class="text-center">No pending feedback. Good job!</td></tr>');
                }

                const trendDates = response.feedbackOverTime.map(item => formatSimpleDate(item.date));
                const trendCounts = response.feedbackOverTime.map(item => item.count);
                chartFeedbackTrend.updateSeries([{ data: trendCounts }]);
                chartFeedbackTrend.updateOptions({ xaxis: { categories: trendDates } });

                const categoryData = [
                    parseFloat(response.categoryRatings.food).toFixed(2),
                    parseFloat(response.categoryRatings.staff).toFixed(2),
                    parseFloat(response.categoryRatings.environment).toFixed(2)
                ];
                chartCategoryRatings.updateSeries([{ data: categoryData }]);

                const distributionLabels = response.ratingsDistribution.map(item => item.rating_label);
                const distributionCounts = response.ratingsDistribution.map(item => item.count);
                chartRatingsDistribution.updateSeries([{ data: distributionCounts }]);
                chartRatingsDistribution.updateOptions({ xaxis: { categories: distributionLabels } });

            },
            error: function (xhr) {
                console.error('Failed to load dashboard analytics:', xhr);
            }
        });
    }

    loadDashboardData();
});
