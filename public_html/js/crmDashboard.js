$(document).ready(function () {
    const numberFormatter = new Intl.NumberFormat('en-US');

    const bookingsTrendOptions = {
        chart: {
            type: 'area',
            height: 340,
            toolbar: { show: false },
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        series: [
            {
                name: 'Bookings',
                data: [],
            },
        ],
        xaxis: {
            categories: [],
            labels: { rotate: -45 },
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
        },
        tooltip: {
            shared: true,
            x: { format: 'MMM d, yyyy' },
        },
        noData: { text: 'Loading booking trend...' },
        colors: ['#7367F0'],
    };

    const bookingStatusOptions = {
        chart: {
            type: 'donut',
            height: 320,
        },
        labels: [],
        series: [],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val ? `${val.toFixed(0)}%` : val;
            },
        },
        legend: {
            position: 'bottom',
            fontSize: '14px',
        },
        noData: { text: 'Loading status data...' },
        colors: ['#EA5455', '#28C76F', '#FF9F43', '#7367F0', '#1E90FF'],
    };

    const categoryRatingsOptions = {
        chart: {
            type: 'radar',
            height: 240,
        },
        series: [
            {
                name: 'Average Rating',
                data: [],
            },
        ],
        xaxis: {
            categories: ['Food', 'Staff', 'Environment'],
        },
        yaxis: {
            min: 0,
            max: 5,
            tickAmount: 5,
            labels: {
                formatter: function (val) {
                    return val.toFixed(0);
                },
            },
        },
        markers: {
            size: 4,
        },
        noData: { text: 'Loading ratings...' },
        colors: ['#28C76F'],
    };

    const ratingsDistributionOptions = {
        chart: {
            type: 'bar',
            height: 240,
            toolbar: { show: false },
        },
        plotOptions: {
            bar: {
                distributed: true,
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 4,
            },
        },
        dataLabels: { enabled: false },
        series: [
            {
                name: 'Count',
                data: [],
            },
        ],
        xaxis: {
            categories: [],
        },
        legend: { show: false },
        noData: { text: 'Loading distribution...' },
        colors: ['#EA5455', '#FF9F43', '#7367F0', '#28C76F', '#00CFE8'],
    };

    const chartBookingsTrend = new ApexCharts(document.querySelector('#chart-bookings-trend'), bookingsTrendOptions);
    chartBookingsTrend.render();

    const chartBookingStatus = new ApexCharts(document.querySelector('#chart-booking-status'), bookingStatusOptions);
    chartBookingStatus.render();

    const chartCategoryRatings = new ApexCharts(document.querySelector('#chart-category-ratings'), categoryRatingsOptions);
    chartCategoryRatings.render();

    const chartRatingsDistribution = new ApexCharts(
        document.querySelector('#chart-ratings-distribution'),
        ratingsDistributionOptions
    );
    chartRatingsDistribution.render();

    function buildScheduleDisplay(date, time) {
        if (!date) {
            return '—';
        }

        const combined = time ? `${date} ${time}` : date;
        const formatted = dayjs(combined);

        if (!formatted.isValid()) {
            return dayjs(date).format('MMM D, YYYY');
        }

        return formatted.format(time ? 'MMM D, YYYY • h:mm A' : 'MMM D, YYYY');
    }

    function escapeHtml(value) {
        return $("<div/>").text(value ?? "").html();
    }

    function truncateText(text, limit = 100) {
        if (!text) {
            return "";
        }
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
            const name = escapeHtml(item.name || "Anonymous");
            const rating = item.overall_rating != null ? Number(item.overall_rating).toFixed(1) : "0.0";
            const dateText = item.created_at ? dayjs(item.created_at).format("MMM D, YYYY") : "—";
            const messageText = truncateText(item.message?.trim() ?? "", 120);
            const messageHtml = messageText
                ? escapeHtml(messageText)
                : '<span class="text-muted fst-italic">No written feedback.</span>';

            const listItem = `
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
            `;

            $container.append(listItem);
        });
    }

    function loadDashboardData() {
        $.ajax({
            url: '/customer-service/dashboard-analytics',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                const kpis = response.kpis || {};
                const avgRatingValue = kpis.averageRating != null ? Number(kpis.averageRating).toFixed(2) : '0.00';
                $('#kpi-average-rating').text(`${avgRatingValue} ★`);
                $('#kpi-upcoming-bookings').text(numberFormatter.format(kpis.upcomingBookings || 0));
                $('#kpi-pending-bookings').text(numberFormatter.format(kpis.pendingBookings || 0));
                $('#kpi-feedback-pending').text(numberFormatter.format(kpis.pendingFeedback || 0));

                const trendData = response.bookingsTrend || [];
                const trendCategories = trendData.map((item) => {
                    const parsed = dayjs(item.date);
                    return parsed.isValid() ? parsed.format('MMM D') : item.date || '';
                });
                const trendCounts = trendData.map((item) => item.count || 0);
                chartBookingsTrend.updateSeries([{ data: trendCounts }]);
                chartBookingsTrend.updateOptions({ xaxis: { categories: trendCategories } });

                const statusData = response.statusBreakdown || [];
                const statusLabels = statusData.map((item) => item.label);
                const statusCounts = statusData.map((item) => item.count || 0);
                chartBookingStatus.updateOptions({ labels: statusLabels });
                chartBookingStatus.updateSeries(statusCounts);

                const $bookingsTableBody = $('#table-upcoming-bookings tbody');
                $bookingsTableBody.empty();
                const upcomingBookings = response.upcomingBookings || [];
                if (upcomingBookings.length === 0) {
                    $bookingsTableBody.append(
                        '<tr><td colspan="5" class="text-center text-muted">No upcoming bookings scheduled.</td></tr>'
                    );
                } else {
                    upcomingBookings.forEach((booking) => {
                        const schedule = buildScheduleDisplay(booking.date, booking.time);
                        const statusDisplay = booking.status_badge || booking.status_label || '—';
                        const rowHtml = `
                            <tr>
                                <td>${booking.name || '—'}</td>
                                <td>${numberFormatter.format(booking.people || 0)}</td>
                                <td>${schedule}</td>
                                <td>${booking.table || 'Not assigned'}</td>
                                <td>${statusDisplay}</td>
                            </tr>
                        `;
                        $bookingsTableBody.append(rowHtml);
                    });
                }

                const categoryRatings = response.categoryRatings || {};
                chartCategoryRatings.updateSeries([
                    {
                        data: [
                            parseFloat(Number(categoryRatings.food || 0).toFixed(2)),
                            parseFloat(Number(categoryRatings.staff || 0).toFixed(2)),
                            parseFloat(Number(categoryRatings.environment || 0).toFixed(2)),
                        ],
                    },
                ]);

                const ratingsDistribution = response.ratingsDistribution || [];
                chartRatingsDistribution.updateSeries([
                    {
                        data: ratingsDistribution.map((item) => item.count || 0),
                    },
                ]);
                chartRatingsDistribution.updateOptions({
                    xaxis: {
                        categories: ratingsDistribution.map((item) => item.rating_label),
                    },
                });

                renderFeedbackList(
                    response.topRatedFeedback || [],
                    $("#list-top-feedback"),
                    "No top rated feedback yet."
                );

                renderFeedbackList(
                    response.recentFeedback || [],
                    $("#list-recent-feedback"),
                    "No feedback submitted recently."
                );
            },
            error: function (xhr) {
                console.error('Failed to load dashboard analytics:', xhr);
            },
        });
    }

    loadDashboardData();
});
