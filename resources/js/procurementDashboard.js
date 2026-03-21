import $ from 'jquery';
import Swal from 'sweetalert2';

$(document).ready(function () {
    const $refreshButton = $("#btn-refresh-dashboard");
    if ($refreshButton.length === 0) {
        return;
    }

    const numberFormatter = new Intl.NumberFormat("en-US");
    const currencyFormatter = new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2,
    });

    const $recentPrBody = $("#recent-prs-body");
    const $recentPoBody = $("#recent-pos-body");
    const $topSuppliersList = $("#top-suppliers-list");
    const $summaryList = $("#dashboard-summary");

    function escapeHTML(value) {
        if (value === null || value === undefined) {
            return "";
        }
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatNumber(value) {
        const numeric = Number(value);
        if (Number.isNaN(numeric)) {
            return "0";
        }
        return numberFormatter.format(numeric);
    }

    function formatCurrency(value) {
        const numeric = Number(value);
        if (Number.isNaN(numeric)) {
            return currencyFormatter.format(0);
        }
        return currencyFormatter.format(numeric);
    }

    function formatDate(value) {
        if (!value) {
            return "—";
        }
        if (typeof dayjs === "function") {
            const parsed = dayjs(value);
            if (parsed.isValid()) {
                return parsed.format("MMM D, YYYY");
            }
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return "—";
        }
        return date.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric",
        });
    }

    function setKpiValue(selector, value, { format = "number" } = {}) {
        const $target = $(selector);
        if ($target.length === 0) {
            return;
        }
        if (format === "currency") {
            $target.text(formatCurrency(value));
        } else {
            $target.text(formatNumber(value));
        }
    }

    function setTableLoading($target, colspan) {
        $target.html(
            `<tr><td colspan="${colspan}" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>`
        );
    }

    function setListLoading($target, text) {
        $target.html(
            `<li class="list-group-item text-muted"><span class="spinner-border spinner-border-sm me-2"></span>${text}</li>`
        );
    }

    function setSummaryLoading() {
        $summaryList.html(
            '<li class="text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Loading snapshot...</li>'
        );
    }

    function updateKpis(kpis) {
        setKpiValue("#kpi-pending-pr", kpis?.pendingPR ?? 0);
        setKpiValue("#kpi-open-po", kpis?.openPO ?? 0);
        setKpiValue("#kpi-overdue-po", kpis?.overduePO ?? 0);
        setKpiValue("#kpi-month-spend", kpis?.monthSpend ?? 0, { format: "currency" });
    }

    function updateRecentPRs(items) {
        if (!items || items.length === 0) {
            $recentPrBody.html(
                '<tr><td colspan="4" class="text-center text-muted py-4">No purchase requests found.</td></tr>'
            );
            return;
        }

        const rows = items
            .map((item) => {
                const reference = escapeHTML(item.reference ?? `PR-${item.id}`);
                const requestedOn = formatDate(item.requested_at);
                const requester = item.requested_by
                    ? escapeHTML(item.requested_by)
                    : '<span class="text-muted">—</span>';
                const status = item.status_html ?? '<span class="badge bg-secondary">Unknown</span>';

                return `
                    <tr>
                        <td class="fw-semibold">${reference}</td>
                        <td>${requestedOn}</td>
                        <td>${requester}</td>
                        <td>${status}</td>
                    </tr>
                `;
            })
            .join("");

        $recentPrBody.html(rows);
    }

    function updateRecentPOs(items) {
        if (!items || items.length === 0) {
            $recentPoBody.html(
                '<tr><td colspan="4" class="text-center text-muted py-4">No purchase orders found.</td></tr>'
            );
            return;
        }

        const rows = items
            .map((item) => {
                const reference = escapeHTML(item.reference ?? `PO-${item.id}`);
                const supplier = item.supplier_name
                    ? escapeHTML(item.supplier_name)
                    : '<span class="text-muted">No supplier</span>';
                const orderDate = formatDate(item.order_date);
                const status = item.status_html ?? '<span class="badge bg-secondary">Unknown</span>';
                const overdueBadge = item.is_overdue
                    ? '<span class="badge bg-danger ms-2">Overdue</span>'
                    : '';

                return `
                    <tr>
                        <td class="fw-semibold">${reference}</td>
                        <td>${supplier}</td>
                        <td>${orderDate}</td>
                        <td>${status}${overdueBadge}</td>
                    </tr>
                `;
            })
            .join("");

        $recentPoBody.html(rows);
    }

    function updateTopSuppliers(suppliers) {
        if (!suppliers || suppliers.length === 0) {
            $topSuppliersList.html(
                '<li class="list-group-item text-muted">No supplier spend recorded this month.</li>'
            );
            return;
        }

        const items = suppliers
            .map((supplier) => {
                const name = escapeHTML(supplier.supplier_name ?? "Unknown supplier");
                const total = formatCurrency(supplier.total ?? 0);
                return `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${name}</span>
                        <span class="fw-semibold">${total}</span>
                    </li>
                `;
            })
            .join("");

        $topSuppliersList.html(items);
    }

    function updateSnapshot(kpis, suppliers) {
        const items = [];

        const pendingPR = kpis?.pendingPR ?? 0;
        const openPO = kpis?.openPO ?? 0;
        const overduePO = kpis?.overduePO ?? 0;
        const monthSpend = kpis?.monthSpend ?? 0;

        items.push(`Pending purchase requests: <strong>${formatNumber(pendingPR)}</strong>`);
        items.push(`Open purchase orders: <strong>${formatNumber(openPO)}</strong>`);
        items.push(`Overdue deliveries: <strong>${formatNumber(overduePO)}</strong>`);
        items.push(`Spend this month: <strong>${formatCurrency(monthSpend)}</strong>`);

        if (Array.isArray(suppliers) && suppliers.length > 0) {
            const top = suppliers[0];
            items.push(
                `Top supplier: <strong>${escapeHTML(top.supplier_name ?? 'Unknown')}</strong> (${formatCurrency(
                    top.total ?? 0
                )})`
            );
        }

        if (overduePO > 0) {
            items.push('<span class="text-danger">Action required: follow up on overdue deliveries.</span>');
        }

        $summaryList.html(items.map((item) => `<li class="mb-2">${item}</li>`).join(""));
    }

    // ── Forecasting ──────────────────────────────────────────────────────────

    let monthlySpendChart = null;

    let forecastYear       = new Date().getFullYear();
    let forecastMonthStart = 1;
    let forecastMonthEnd   = 12;
    let chartViewMode      = "period"; // "period" | "yearly"

    function updateForecastKpi(forecastedSpend, forecastedLabel, granularity) {
        const $kpiSpend = $("#kpi-forecast-spend");
        const $kpiMonth = $("#kpi-forecast-month");
        const $note     = $("#forecast-avg-note");

        if ($kpiSpend.length) {
            $kpiSpend.text(forecastedSpend > 0 ? formatCurrency(forecastedSpend) : "No data yet");
        }
        if ($kpiMonth.length) {
            $kpiMonth.text(forecastedLabel ? escapeHTML(forecastedLabel) : "");
        }
        if ($note.length) {
            const noteMap = {
                quarterly : "2-quarter moving average of completed PO spend.",
                monthly   : "3-month moving average of completed PO spend.",
                yearly    : "3-year moving average of completed PO spend.",
            };
            $note.text(noteMap[granularity] ?? "Moving average of completed PO spend.");
        }
    }

    function updateTopReorderedItems(items) {
        const $list = $("#top-reordered-items");
        if (!$list.length) return;

        if (!items || items.length === 0) {
            $list.html('<li class="list-group-item text-muted">No reorder data available.</li>');
            return;
        }

        const html = items
            .map((item) => {
                const name  = escapeHTML(item.item_name ?? "Unknown item");
                const count = formatNumber(item.order_count ?? 0);
                const qty   = formatNumber(item.total_qty ?? 0);
                return `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>${name}</span>
                        <span class="text-muted small">${count} order${item.order_count !== 1 ? "s" : ""} &middot; ${qty} units</span>
                    </li>
                `;
            })
            .join("");

        $list.html(html);
    }

    function renderSpendChart(spendData, forecastedSpend, forecastedLabel, granularity) {
        const chartEl = document.getElementById("chart-monthly-spend");
        if (!chartEl || typeof ApexCharts === "undefined") return;

        const labels  = spendData.map((d) => d.label);
        const actuals = spendData.map((d) => parseFloat(d.spend) || 0);

        labels.push(forecastedLabel ?? "Next Period");
        const forecastSeries = new Array(spendData.length).fill(null);
        forecastSeries.push(parseFloat(forecastedSpend) || 0);
        const actualSeries = [...actuals, null];

        const $subtitle = $("#forecast-chart-subtitle");
        if ($subtitle.length) {
            const subtitleMap = {
                quarterly : "Quarterly view",
                monthly   : "Monthly view",
                yearly    : "Yearly view (5 years)",
            };
            $subtitle.text(subtitleMap[granularity] ?? "");
        }

        const options = {
            series: [
                { name: "Actual Spend", data: actualSeries },
                { name: "Forecast",     data: forecastSeries },
            ],
            chart: {
                type: "bar",
                height: 300,
                toolbar: { show: false },
                stacked: false,
            },
            plotOptions: {
                bar: { columnWidth: "55%", borderRadius: 4 },
            },
            dataLabels: { enabled: false },
            colors: ["#435ebe", "#f9c74f"],
            stroke: { show: true, width: 2, colors: ["transparent"] },
            xaxis: {
                categories: labels,
                labels: { style: { fontSize: "11px" } },
            },
            yaxis: {
                labels: {
                    formatter: (val) => {
                        if (val === null || val === undefined) return "";
                        if (val >= 1000) return "₱" + (val / 1000).toFixed(0) + "k";
                        return "₱" + val.toFixed(0);
                    },
                },
            },
            tooltip: {
                y: { formatter: (val) => (val !== null ? formatCurrency(val) : "—") },
            },
            legend: { position: "top", horizontalAlign: "right" },
        };

        if (monthlySpendChart) {
            monthlySpendChart.updateOptions(options);
        } else {
            monthlySpendChart = new ApexCharts(chartEl, options);
            monthlySpendChart.render();
        }
    }

    function loadForecastData() {
        setListLoading($("#top-reordered-items"), "Loading items...");

        const params = { view: chartViewMode };
        if (chartViewMode === "period") {
            params.year        = forecastYear;
            params.month_start = forecastMonthStart;
            params.month_end   = forecastMonthEnd;
        }

        $.ajax({
            url: "/procurement/spend-forecast",
            type: "GET",
            dataType: "json",
            data: params,
            success: function (response) {
                const granularity = response?.granularity ?? "quarterly";
                updateForecastKpi(
                    response?.forecastedNextPeriod ?? 0,
                    response?.forecastedLabel ?? "",
                    granularity
                );
                updateTopReorderedItems(response?.topReorderedItems ?? []);
                renderSpendChart(
                    response?.spendData ?? [],
                    response?.forecastedNextPeriod ?? 0,
                    response?.forecastedLabel ?? "Next Period",
                    granularity
                );
            },
            error: function () {
                $("#top-reordered-items").html(
                    '<li class="list-group-item text-danger">Unable to load forecast data.</li>'
                );
                $("#kpi-forecast-spend").text("Error");
            },
        });
    }

    // ── Filter change handlers ────────────────────────────────────────────────

    $("#forecast-year").on("change", function () {
        forecastYear = parseInt($(this).val(), 10);
        loadForecastData();
    });

    $("#forecast-month-start").on("change", function () {
        forecastMonthStart = parseInt($(this).val(), 10);
        if (forecastMonthEnd < forecastMonthStart) {
            forecastMonthEnd = forecastMonthStart;
            $("#forecast-month-end").val(forecastMonthEnd);
        }
        loadForecastData();
    });

    $("#forecast-month-end").on("change", function () {
        forecastMonthEnd = parseInt($(this).val(), 10);
        if (forecastMonthStart > forecastMonthEnd) {
            forecastMonthStart = forecastMonthEnd;
            $("#forecast-month-start").val(forecastMonthStart);
        }
        loadForecastData();
    });

    // ── Chart view toggle (Period / Yearly) ───────────────────────────────────

    $("#chart-view-toggle").on("click", "button[data-view]", function () {
        const newMode = $(this).data("view");
        if (newMode === chartViewMode) return;

        chartViewMode = newMode;

        $("#chart-view-toggle button").each(function () {
            const isActive = $(this).data("view") === chartViewMode;
            $(this)
                .toggleClass("btn-primary", isActive)
                .toggleClass("btn-outline-primary", !isActive)
                .toggleClass("active", isActive);
        });

        if (chartViewMode === "yearly") {
            $("#forecast-period-filters").hide();
        } else {
            $("#forecast-period-filters").show();
        }

        loadForecastData();
    });

    // ─────────────────────────────────────────────────────────────────────────

    function loadDashboardData() {
        setTableLoading($recentPrBody, 4);
        setTableLoading($recentPoBody, 4);
        setListLoading($topSuppliersList, "Loading suppliers...");
        setSummaryLoading();
        updateKpis({ pendingPR: 0, openPO: 0, overduePO: 0, monthSpend: 0 });

        $.ajax({
            url: "/procurement/dashboard-analytics",
            type: "GET",
            dataType: "json",
            success: function (response) {
                const kpis = response?.kpis ?? {};
                const recentPRs = response?.recentPRs ?? [];
                const recentPOs = response?.recentPOs ?? [];
                const topSuppliers = response?.topSuppliers ?? [];

                updateKpis(kpis);
                updateRecentPRs(recentPRs);
                updateRecentPOs(recentPOs);
                updateTopSuppliers(topSuppliers);
                updateSnapshot(kpis, topSuppliers);
            },
            error: function () {
                $recentPrBody.html(
                    '<tr><td colspan="4" class="text-center text-danger py-4">Failed to load purchase requests.</td></tr>'
                );
                $recentPoBody.html(
                    '<tr><td colspan="4" class="text-center text-danger py-4">Failed to load purchase orders.</td></tr>'
                );
                $topSuppliersList.html(
                    '<li class="list-group-item text-danger">Unable to retrieve supplier data.</li>'
                );
                $summaryList.html(
                    '<li class="text-danger">Unable to load dashboard summary. Please try again.</li>'
                );
            },
        });
    }

    $refreshButton.on("click", function () {
        loadDashboardData();
        loadForecastData();
    });

    loadDashboardData();
    loadForecastData();
});

