const DASHBOARD_ROOT_ID = "supplier-dashboard-root";

document.addEventListener("DOMContentLoaded", () => {
    const root = document.getElementById(DASHBOARD_ROOT_ID);
    if (!root) {
        return;
    }

    const $refreshBtn = $("#btn-refresh-supplier-dashboard");
    const $statusBreakdownList = $("#status-breakdown-list");
    const $recentOrdersBody = $("#recent-orders-body");
    const $upcomingDeliveriesBody = $("#upcoming-deliveries-body");
    const $activityFeed = $("#supplier-activity-feed");

    function setLoadingState() {
        [
            "#summary-total-products",
            "#summary-active-orders",
            "#summary-pending-shipments",
            "#summary-returns",
            "#summary-redeliveries",
        ].forEach((selector) => {
            $(selector).text("—");
        });

        $statusBreakdownList.html(
            '<li class="list-group-item text-muted">Loading...</li>'
        );
        $recentOrdersBody.html(
            '<tr><td colspan="4" class="text-center text-muted py-3">Loading...</td></tr>'
        );
        $upcomingDeliveriesBody.html(
            '<tr><td colspan="3" class="text-center text-muted py-3">Loading...</td></tr>'
        );
        $activityFeed.html(
            '<li class="list-group-item text-muted">Gathering activity...</li>'
        );
    }

    function renderKpis(kpis = {}) {
        $("#summary-total-products").text(kpis.totalProducts ?? 0);
        $("#summary-active-orders").text(kpis.activeOrders ?? 0);
        $("#summary-pending-shipments").text(kpis.pendingShipments ?? 0);
        $("#summary-returns").text(kpis.returnsPending ?? 0);
        $("#summary-redeliveries").text(kpis.redeliveryPending ?? 0);
    }

    function renderStatusBreakdown(items = []) {
        $statusBreakdownList.empty();
        if (!Array.isArray(items) || items.length === 0) {
            $statusBreakdownList.html(
                '<li class="list-group-item text-muted">No active orders to show.</li>'
            );
            return;
        }
        items.forEach((item) => {
            const statusHtml = item.status_html || item.label || "Status";
            const count = item.count ?? 0;
            const listItem = `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="me-2">${statusHtml}</span>
                    <span class="badge bg-secondary rounded-pill">${count}</span>
                </li>
            `;
            $statusBreakdownList.append(listItem);
        });
    }

    function renderRecentOrders(orders = []) {
        $recentOrdersBody.empty();
        if (!Array.isArray(orders) || orders.length === 0) {
            $recentOrdersBody.append(
                '<tr><td colspan="4" class="text-center text-muted py-3">No recent purchase requests.</td></tr>'
            );
            return;
        }

        orders.forEach((order) => {
            const formattedAmount = Number(order.total_amount ?? 0).toLocaleString(
                undefined,
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }
            );
            const row = `
                <tr>
                    <td>PR-${order.purchase_request_id ?? "—"}</td>
                    <td>${order.requested_date ?? "—"}</td>
                    <td>${order.status_html ?? '<span class="badge bg-secondary">Unknown</span>'}</td>
                    <td>₱ ${formattedAmount}</td>
                </tr>
            `;
            $recentOrdersBody.append(row);
        });
    }

    function renderUpcomingDeliveries(deliveries = []) {
        $upcomingDeliveriesBody.empty();
        if (!Array.isArray(deliveries) || deliveries.length === 0) {
            $upcomingDeliveriesBody.append(
                '<tr><td colspan="3" class="text-center text-muted py-3">No deliveries scheduled.</td></tr>'
            );
            return;
        }

        deliveries.forEach((delivery) => {
            const row = `
                <tr>
                    <td>PO-${delivery.purchase_order_id ?? "—"}</td>
                    <td>${delivery.expected_date ?? "—"}</td>
                    <td>${delivery.status_html ?? '<span class="badge bg-secondary">Unknown</span>'}</td>
                </tr>
            `;
            $upcomingDeliveriesBody.append(row);
        });
    }

    function renderActivityFeed(items = []) {
        $activityFeed.empty();
        if (!Array.isArray(items) || items.length === 0) {
            $activityFeed.html(
                '<li class="list-group-item text-muted">No recent supplier activity.</li>'
            );
            return;
        }

        items.forEach((activity) => {
            const listItem = `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${activity.message || "Status update"}</span>
                    <small class="text-muted ms-3">${activity.timestamp || "—"}</small>
                </li>
            `;
            $activityFeed.append(listItem);
        });
    }

    function fetchDashboardSummary() {
        setLoadingState();
        $.getJSON("/supplier/dashboard-summary")
            .done((response) => {
                const kpis = response?.kpis || {};
                renderKpis(kpis);
                renderStatusBreakdown(response?.statusBreakdown || []);
                renderRecentOrders(response?.recentOrders || []);
                renderUpcomingDeliveries(response?.upcomingDeliveries || []);
                renderActivityFeed(response?.activityFeed || []);
            })
            .fail(() => {
                Toast.fire({
                    title: "Error",
                    text: "Unable to refresh dashboard data.",
                    icon: "error",
                });
            });
    }

    if ($refreshBtn.length) {
        $refreshBtn.on("click", function () {
            fetchDashboardSummary();
        });
    }

    fetchDashboardSummary();
});
