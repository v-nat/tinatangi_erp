const f="supplier-dashboard-root";document.addEventListener("DOMContentLoaded",()=>{if(!document.getElementById(f))return;const c=$("#btn-refresh-supplier-dashboard"),r=$("#status-breakdown-list"),n=$("#recent-orders-body"),d=$("#upcoming-deliveries-body"),i=$("#supplier-activity-feed");function u(){["#summary-total-products","#summary-active-orders","#summary-pending-shipments","#summary-returns","#summary-redeliveries"].forEach(t=>{$(t).text("—")}),r.html('<li class="list-group-item text-muted">Loading...</li>'),n.html('<tr><td colspan="4" class="text-center text-muted py-3">Loading...</td></tr>'),d.html('<tr><td colspan="3" class="text-center text-muted py-3">Loading...</td></tr>'),i.html('<li class="list-group-item text-muted">Gathering activity...</li>')}function o(t={}){$("#summary-total-products").text(t.totalProducts??0),$("#summary-active-orders").text(t.activeOrders??0),$("#summary-pending-shipments").text(t.pendingShipments??0),$("#summary-returns").text(t.returnsPending??0),$("#summary-redeliveries").text(t.redeliveryPending??0)}function m(t=[]){if(r.empty(),!Array.isArray(t)||t.length===0){r.html('<li class="list-group-item text-muted">No active orders to show.</li>');return}t.forEach(e=>{const a=e.status_html||e.label||"Status",s=e.count??0,h=`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="me-2">${a}</span>
                    <span class="badge bg-secondary rounded-pill">${s}</span>
                </li>
            `;r.append(h)})}function p(t=[]){if(n.empty(),!Array.isArray(t)||t.length===0){n.append('<tr><td colspan="4" class="text-center text-muted py-3">No recent purchase requests.</td></tr>');return}t.forEach(e=>{const a=Number(e.total_amount??0).toLocaleString(void 0,{minimumFractionDigits:2,maximumFractionDigits:2}),s=`
                <tr>
                    <td>PR-${e.purchase_request_id??"—"}</td>
                    <td>${e.requested_date??"—"}</td>
                    <td>${e.status_html??'<span class="badge bg-secondary">Unknown</span>'}</td>
                    <td>₱ ${a}</td>
                </tr>
            `;n.append(s)})}function y(t=[]){if(d.empty(),!Array.isArray(t)||t.length===0){d.append('<tr><td colspan="3" class="text-center text-muted py-3">No deliveries scheduled.</td></tr>');return}t.forEach(e=>{const a=`
                <tr>
                    <td>PO-${e.purchase_order_id??"—"}</td>
                    <td>${e.expected_date??"—"}</td>
                    <td>${e.status_html??'<span class="badge bg-secondary">Unknown</span>'}</td>
                </tr>
            `;d.append(a)})}function g(t=[]){if(i.empty(),!Array.isArray(t)||t.length===0){i.html('<li class="list-group-item text-muted">No recent supplier activity.</li>');return}t.forEach(e=>{const a=`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${e.message||"Status update"}</span>
                    <small class="text-muted ms-3">${e.timestamp||"—"}</small>
                </li>
            `;i.append(a)})}function l(){u(),$.getJSON("/supplier/dashboard-summary").done(t=>{const e=(t==null?void 0:t.kpis)||{};o(e),m((t==null?void 0:t.statusBreakdown)||[]),p((t==null?void 0:t.recentOrders)||[]),y((t==null?void 0:t.upcomingDeliveries)||[]),g((t==null?void 0:t.activityFeed)||[])}).fail(()=>{Toast.fire({title:"Error",text:"Unable to refresh dashboard data.",icon:"error"})})}c.length&&c.on("click",function(){l()}),l()});
