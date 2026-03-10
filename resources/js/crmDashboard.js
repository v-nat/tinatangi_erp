import $ from 'jquery';

$(document).ready(function () {
    const numberFormatter = new Intl.NumberFormat('en-US');

    /* ── Chart option definitions ───────────────────────────────────────── */

    const bookingsTrendOptions = {
        chart: { type: 'area', height: 340, toolbar: { show: false } },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        series: [{ name: 'Bookings', data: [] }],
        xaxis: { categories: [], labels: { rotate: -45 } },
        yaxis: { min: 0, forceNiceScale: true },
        tooltip: { shared: true, x: { format: 'MMM d, yyyy' } },
        noData: { text: 'Loading booking trend...' },
        colors: ['#7367F0'],
    };

    const bookingStatusOptions = {
        chart: { type: 'donut', height: 320 },
        labels: [],
        series: [],
        dataLabels: {
            enabled: true,
            formatter: (val) => (val ? `${val.toFixed(0)}%` : val),
        },
        legend: { position: 'bottom', fontSize: '14px' },
        noData: { text: 'Loading status data...' },
        colors: ['#EA5455', '#28C76F', '#FF9F43', '#7367F0', '#1E90FF'],
    };

    const categoryRatingsOptions = {
        chart: { type: 'radar', height: 240 },
        series: [{ name: 'Average Rating', data: [] }],
        xaxis: { categories: ['Food', 'Staff', 'Environment'] },
        yaxis: {
            min: 0,
            max: 5,
            tickAmount: 5,
            labels: { formatter: (val) => val.toFixed(0) },
        },
        markers: { size: 4 },
        noData: { text: 'Loading ratings...' },
        colors: ['#28C76F'],
    };

    const ratingsDistributionOptions = {
        chart: { type: 'bar', height: 240, toolbar: { show: false } },
        plotOptions: {
            bar: { distributed: true, horizontal: false, columnWidth: '55%', borderRadius: 4 },
        },
        dataLabels: { enabled: false },
        series: [{ name: 'Count', data: [] }],
        xaxis: { categories: [] },
        legend: { show: false },
        noData: { text: 'Loading distribution...' },
        colors: ['#EA5455', '#FF9F43', '#7367F0', '#28C76F', '#00CFE8'],
    };

    const forecastOptions = {
        chart: { type: 'line', height: 310, toolbar: { show: false } },
        stroke: {
            width: [2, 2],
            dashArray: [0, 6],
            curve: 'smooth',
        },
        markers: { size: [3, 5] },
        series: [
            { name: 'Actual', data: [] },
            { name: 'Forecast', data: [] },
        ],
        xaxis: {
            categories: [],
            labels: { rotate: -45, style: { fontSize: '11px' } },
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            title: { text: 'Bookings' },
        },
        legend: { position: 'top', horizontalAlign: 'right' },
        colors: ['#7367F0', '#FF9F43'],
        annotations: { xaxis: [] },
        tooltip: { shared: true, intersect: false },
        noData: { text: 'Loading forecast...' },
    };

    /* ── Render charts ───────────────────────────────────────────────────── */

    const chartBookingsTrend = new ApexCharts(
        document.querySelector('#chart-bookings-trend'),
        bookingsTrendOptions
    );
    chartBookingsTrend.render();

    const chartBookingStatus = new ApexCharts(
        document.querySelector('#chart-booking-status'),
        bookingStatusOptions
    );
    chartBookingStatus.render();

    const chartCategoryRatings = new ApexCharts(
        document.querySelector('#chart-category-ratings'),
        categoryRatingsOptions
    );
    chartCategoryRatings.render();

    const chartRatingsDistribution = new ApexCharts(
        document.querySelector('#chart-ratings-distribution'),
        ratingsDistributionOptions
    );
    chartRatingsDistribution.render();

    const chartForecast = new ApexCharts(
        document.querySelector('#chart-booking-forecast'),
        forecastOptions
    );
    chartForecast.render();

    /* ── Helper functions ────────────────────────────────────────────────── */

    function buildScheduleDisplay(date, time) {
        if (!date) return '—';
        const combined = time ? `${date} ${time}` : date;
        const formatted = dayjs(combined);
        if (!formatted.isValid()) return dayjs(date).format('MMM D, YYYY');
        return formatted.format(time ? 'MMM D, YYYY • h:mm A' : 'MMM D, YYYY');
    }

    function escapeHtml(value) {
        return $('<div/>').text(value ?? '').html();
    }

    function truncateText(text, limit = 100) {
        if (!text) return '';
        return text.length > limit ? `${text.substring(0, limit)}…` : text;
    }

    function renderFeedbackList(items, $container, emptyText) {
        $container.empty();
        if (!items || items.length === 0) {
            $container.append(
                `<li class="list-group-item text-muted text-center">${emptyText}</li>`
            );
            return;
        }
        items.forEach((item) => {
            const name = escapeHtml(item.name || 'Anonymous');
            const rating =
                item.overall_rating != null ? Number(item.overall_rating).toFixed(1) : '0.0';
            const dateText = item.created_at ? dayjs(item.created_at).format('MMM D, YYYY') : '—';
            const messageText = truncateText(item.message?.trim() ?? '', 120);
            const messageHtml = messageText
                ? escapeHtml(messageText)
                : '<span class="text-muted fst-italic">No written feedback.</span>';

            $container.append(`
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="pe-3">
                            <h6 class="mb-1">${name}</h6>
                            <p class="mb-1 small text-muted">${messageHtml}</p>
                            <small class="text-muted">${dateText}</small>
                        </div>
                        <div class="ms-2 text-nowrap">
                            <span class="badge bg-warning text-dark">
                                <i class="fa-solid fa-star me-1"></i>${rating}
                            </span>
                        </div>
                    </div>
                </li>
            `);
        });
    }

    function renderForecastInsights(forecast) {
        const $list = $('#forecast-insights-list');
        $list.empty();

        if (!forecast || forecast.length === 0) {
            $('#forecast-peak-day').text('—');
            $('#forecast-total-week').text('—');
            return;
        }

        const maxPredicted = Math.max(...forecast.map((d) => d.predicted));
        const totalPredicted = forecast.reduce((sum, d) => sum + d.predicted, 0);

        const peakDay = forecast.find((d) => d.predicted === maxPredicted);
        $('#forecast-peak-day').text(
            peakDay ? dayjs(peakDay.date).format('ddd, MMM D') : '—'
        );
        $('#forecast-total-week').text(Math.round(totalPredicted));

        forecast.forEach((d) => {
            const pct = maxPredicted > 0 ? (d.predicted / maxPredicted) * 100 : 0;
            const isPeak = d.predicted === maxPredicted;
            $list.append(`
                <div class="d-flex align-items-center mb-2">
                    <div class="me-2 text-muted" style="width:80px;font-size:12px;">
                        ${dayjs(d.date).format('ddd M/D')}
                    </div>
                    <div class="flex-grow-1">
                        <div class="progress" style="height:14px;border-radius:7px;">
                            <div class="progress-bar ${isPeak ? 'bg-warning' : 'bg-primary'}"
                                 style="width:${pct.toFixed(1)}%"></div>
                        </div>
                    </div>
                    <div class="ms-2 fw-bold text-end" style="width:36px;font-size:13px;">
                        ${d.predicted}
                    </div>
                </div>
            `);
        });
    }

    /* ── Main data loader ────────────────────────────────────────────────── */

    function loadDashboardData() {
        $.ajax({
            url: '/customer-service/dashboard-analytics',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                /* KPIs */
                const kpis = response.kpis || {};
                const avgRatingValue =
                    kpis.averageRating != null
                        ? Number(kpis.averageRating).toFixed(2)
                        : '0.00';
                $('#kpi-average-rating').text(`${avgRatingValue} ★`);
                $('#kpi-upcoming-bookings').text(
                    numberFormatter.format(kpis.upcomingBookings || 0)
                );
                $('#kpi-pending-bookings').text(
                    numberFormatter.format(kpis.pendingBookings || 0)
                );
                $('#kpi-feedback-pending').text(
                    numberFormatter.format(kpis.pendingFeedback || 0)
                );
                $('#kpi-active-announcements').text(
                    numberFormatter.format(kpis.activeAnnouncements || 0)
                );

                /* Booking trend (30 days) */
                const trendData = response.bookingsTrend || [];
                const trendCategories = trendData.map((item) => {
                    const parsed = dayjs(item.date);
                    return parsed.isValid() ? parsed.format('MMM D') : item.date || '';
                });
                chartBookingsTrend.updateSeries([
                    { data: trendData.map((item) => item.count || 0) },
                ]);
                chartBookingsTrend.updateOptions({
                    xaxis: { categories: trendCategories },
                });

                /* Booking status donut */
                const statusData = response.statusBreakdown || [];
                chartBookingStatus.updateOptions({
                    labels: statusData.map((item) => item.label),
                });
                chartBookingStatus.updateSeries(
                    statusData.map((item) => item.count || 0)
                );

                /* Upcoming bookings table */
                const $tbody = $('#table-upcoming-bookings tbody');
                $tbody.empty();
                const upcomingBookings = response.upcomingBookings || [];
                if (upcomingBookings.length === 0) {
                    $tbody.append(
                        '<tr><td colspan="5" class="text-center text-muted">No upcoming bookings scheduled.</td></tr>'
                    );
                } else {
                    upcomingBookings.forEach((booking) => {
                        const schedule = buildScheduleDisplay(booking.date, booking.time);
                        const statusDisplay =
                            booking.status_badge || booking.status_label || '—';
                        $tbody.append(`
                            <tr>
                                <td>${booking.name || '—'}</td>
                                <td>${numberFormatter.format(booking.people || 0)}</td>
                                <td>${schedule}</td>
                                <td>${booking.table || 'Not assigned'}</td>
                                <td>${statusDisplay}</td>
                            </tr>
                        `);
                    });
                }

                /* Category ratings radar */
                const cat = response.categoryRatings || {};
                chartCategoryRatings.updateSeries([
                    {
                        data: [
                            parseFloat(Number(cat.food || 0).toFixed(2)),
                            parseFloat(Number(cat.staff || 0).toFixed(2)),
                            parseFloat(Number(cat.environment || 0).toFixed(2)),
                        ],
                    },
                ]);

                /* Ratings distribution bar */
                const dist = response.ratingsDistribution || [];
                chartRatingsDistribution.updateSeries([
                    { data: dist.map((item) => item.count || 0) },
                ]);
                chartRatingsDistribution.updateOptions({
                    xaxis: { categories: dist.map((item) => item.rating_label) },
                });

                /* Feedback lists */
                renderFeedbackList(
                    response.topRatedFeedback || [],
                    $('#list-top-feedback'),
                    'No top rated feedback yet.'
                );
                renderFeedbackList(
                    response.recentFeedback || [],
                    $('#list-recent-feedback'),
                    'No feedback submitted recently.'
                );

                /* 7-day forecast chart */
                const recentActual = response.recentActual || [];
                const forecast = response.forecast || [];

                const allLabels = [
                    ...recentActual.map((d) => dayjs(d.date).format('MMM D')),
                    ...forecast.map((d) => dayjs(d.date).format('MMM D')),
                ];
                const actualSeries = [
                    ...recentActual.map((d) => d.count),
                    ...forecast.map(() => null),
                ];
                const forecastSeries = [
                    ...recentActual.map(() => null),
                    ...forecast.map((d) => d.predicted),
                ];

                // Annotation line at the boundary between actual and forecast
                const boundaryLabel =
                    recentActual.length > 0
                        ? allLabels[recentActual.length - 1]
                        : null;

                chartForecast.updateOptions({
                    xaxis: { categories: allLabels },
                    annotations: {
                        xaxis: boundaryLabel
                            ? [
                                  {
                                      x: boundaryLabel,
                                      borderColor: '#ea5455',
                                      borderWidth: 2,
                                      label: {
                                          style: {
                                              color: '#fff',
                                              background: '#ea5455',
                                              fontSize: '11px',
                                          },
                                          text: 'Today →',
                                          position: 'top',
                                          orientation: 'horizontal',
                                      },
                                  },
                              ]
                            : [],
                    },
                });
                chartForecast.updateSeries([
                    { name: 'Actual', data: actualSeries },
                    { name: 'Forecast', data: forecastSeries },
                ]);

                renderForecastInsights(forecast);
            },
            error: function (xhr) {
                console.error('Failed to load dashboard analytics:', xhr);
            },
        });
    }

    loadDashboardData();
});
