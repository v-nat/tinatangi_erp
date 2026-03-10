import{r as le}from"./reloadTable-EpUDf5t5.js";$(document).ready(function(){const Y=$("#recentItems").DataTable({autoWidth:!1,processing:!0,serverSide:!1,ajax:{url:"/inventory/recent-items",type:"GET",dataSrc:"data"},columns:[{data:null,render:function(e,t,n,r){return r.row+1},className:"text-center",width:"40px",orderable:!1},{data:"sku",className:"dt-left"},{data:"item_name",className:"dt-left"},{data:"category",className:"dt-left"},{data:"stock_level",className:"dt-left",render:function(e,t,n){if(t==="display"||t==="filter"){if(n.stock_display)return n.stock_display;const r=n.stock_level_formatted??Number(e||0).toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2}),a=n.unit?` ${n.unit}`:"";return`${r}${a}`.trim()}return e}},{data:"cost_price",className:"dt-left",render:function(e,t,n){return"₱ "+parseFloat(e).toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2})}},{data:"expiration_date",className:"dt-left",render:function(e,t,n){if(!e)return'<span class="text-muted">—</span>';if(t==="sort"||t==="type")return e;const r=new Date;r.setHours(0,0,0,0);const a=new Date(e),s=Math.ceil((a-r)/864e5);return s<0?'<span class="badge bg-danger">Expired</span>':s<=30?`<span class="badge bg-warning text-dark">${e}</span>`:`<span class="text-success">${e}</span>`}},{data:"status",className:"text-center",width:"130px"},{data:"received_at",className:"dt-left",render:function(e,t,n){return t==="sort"||t==="type"?n.received_at_raw||"":e?`<span class="text-muted small">${e}</span>`:'<span class="text-muted">—</span>'}}],order:[[8,"desc"]]}),h="/logo.png",Q=new Intl.NumberFormat("en-US"),b=new Intl.NumberFormat("en-US",{style:"currency",currency:"PHP",minimumFractionDigits:2,maximumFractionDigits:2}),m=$("#inventory-best-seller-summary").length>0,u={weekly:null,monthly:null};let x=null;const v=$("#inventory-best-seller-range"),f=$("#inventory-best-seller-overlay"),w=$("#inventory-best-seller-overlay-grid"),X=$("#inventory-best-seller-overlay-title"),Z=$("#inventory-best-seller-overlay-subtitle"),ee={weekly:{title:$("#inventory-weekly-title"),category:$("#inventory-weekly-category"),units:$("#inventory-weekly-units"),revenue:$("#inventory-weekly-revenue"),image:$("#inventory-weekly-image"),rank:$("#inventory-weekly-rank"),card:$('.best-seller-summary-card[data-mode="weekly"]')},monthly:{title:$("#inventory-monthly-title"),category:$("#inventory-monthly-category"),units:$("#inventory-monthly-units"),revenue:$("#inventory-monthly-revenue"),image:$("#inventory-monthly-image"),rank:$("#inventory-monthly-rank"),card:$('.best-seller-summary-card[data-mode="monthly"]')}};function L(e){if(e==null||e==="")return"0";const t=Number(e);return Number.isNaN(t)?"0":Q.format(t)}function F(e){if(e==null||e==="")return b.format(0);const t=Number(e);return Number.isNaN(t)?b.format(0):b.format(t)}function N(e){return e==null?"":String(e).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#039;")}function P(e){if(!e||e==="N/A")return h;if(/^https?:\/\//i.test(e))return e;const t=String(e).replace(/^\/+/,"");return t.startsWith("storage/")?`/${t}`:t.startsWith("app/public/")?`/storage/${t.replace(/^app\/public\//,"")}`:t.startsWith("public/")?`/storage/${t.replace(/^public\//,"")}`:`/storage/app/public/${t}`}function R(e){return!e||!Array.isArray(e.categories)?[]:e.categories.flatMap(t=>(Array.isArray(t.items)?t.items:[]).map(r=>({...r,category_id:t.category_id,category_name:t.category_name})).filter(r=>Number(r.total_units)>0))}function te(e){if(!e||!Array.isArray(e.categories))return e;const t=e.categories.map(n=>{const r=Array.isArray(n.items)?n.items:[];if(!r.length)return null;const s=[...r].sort((o,i)=>Number(i.total_units||0)-Number(o.total_units||0))[0];return!s||Number(s.total_units)<=0?null:{...n,items:[s]}}).filter(Boolean);return{...e,categories:t}}function D(e){if(!e||!Array.isArray(e.categories))return e;const t=te(e),r=[...R(t)].sort((o,i)=>Number(i.total_units||0)-Number(o.total_units||0)),a=new Map;r.forEach((o,i)=>{const l=`${o.category_id}-${o.product_id}`;a.set(l,i+1)});const s=(t.categories||[]).map(o=>{const i=(o.items||[]).map(l=>{const c=`${o.category_id}-${l.product_id}`,d=a.get(c)??null;return{...l,rank:d,global_rank:d}});return{...o,items:i}});return{...t,categories:s}}function ne(e){const t=R(e);return t.length?[...t].sort((n,r)=>Number(r.total_units)-Number(n.total_units))[0]:null}function S(e,t){const n=ee[e];if(!m||!n)return;n.title.text(t.title??"-"),n.category.text(t.category??"-"),n.units.text(t.units??"0"),n.revenue.text(t.revenue??b.format(0)),n.image.attr("src",t.image||h),n.rank&&n.rank.length&&n.rank.text(t.rank??"-");const r=!!t.interactive;n.card.css("pointer-events",r?"auto":"none"),n.card.css("cursor",r?"pointer":"not-allowed"),n.card.toggleClass("opacity-50",!r),n.card.attr("aria-disabled",r?"false":"true"),n.card.attr("data-has-data",r?"true":"false")}function M(e){S(e,{title:"Loading...",category:"Please wait",units:"—",revenue:"—",image:h,rank:null,interactive:!1})}function O(e){S(e,{title:"Failed to load",category:"We could not retrieve this data.",units:"—",revenue:"—",image:h,rank:null,interactive:!1})}function re(e){S(e,{title:"No sales yet",category:"No best sellers recorded",units:"0",revenue:b.format(0),image:h,rank:null,interactive:!1})}function ae(){var n,r;if(!m||!v.length)return;const e=(n=u.weekly)!=null&&n.label?`Weekly: ${u.weekly.label}`:"Weekly: No data yet",t=(r=u.monthly)!=null&&r.label?`Monthly: ${u.monthly.label}`:"Monthly: No data yet";v.text(`${e} • ${t}`)}function B(e){const t=u[e];if(!t){O(e);return}const n=ne(t);if(!n){re(e);return}S(e,{title:n.product_name||"Unnamed Product",category:n.category_name||"Uncategorized",units:L(n.total_units),revenue:F(n.total_revenue),image:P(n.image),rank:n.global_rank||n.rank||1,interactive:!0})}function j(e){if(!m||!f.length)return;const t=u[e],n=e==="weekly"?"Weekly Best Sellers":"Monthly Best Sellers",r=(t==null?void 0:t.label)||"Current period";X.text(n),Z.text(`Category leaders ranked by units sold for ${r}.`);const a=R(t);if(!a.length){w.html(`
                <div class="col-12">
                    <div class="text-center text-muted py-5">No sales data is available for this period yet.</div>
                </div>
            `);return}const o=[...a].sort((i,l)=>{const c=Number(i.global_rank||i.rank||0)-Number(l.global_rank||l.rank||0);return c!==0?c:Number(l.total_units||0)-Number(i.total_units||0)}).map(i=>{const l=P(i.image),c=L(i.total_units),d=F(i.total_revenue),I=F(i.average_unit_price);return`
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="position-relative me-3" style="width:64px; height:64px;">
                                        <img src="${l}" alt="${N(i.product_name||"Best seller")}" class="rounded-circle w-100 h-100 object-fit-cover border border-2 border-light">
                                        <span class="badge bg-success position-absolute top-0 start-0 translate-middle rounded-pill px-2 py-1">#${N(i.global_rank||i.rank||"-")}</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">${N(i.product_name||"Unnamed Product")}</h5>
                                        <small class="text-muted d-block">${N(i.category_name||"Uncategorized")}</small>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                        <span>Units Sold</span>
                                        <span class="fw-semibold text-dark">${c}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                        <span>Total Revenue</span>
                                        <span class="fw-semibold text-dark">${d}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <span>Avg Unit Price</span>
                                        <span class="fw-semibold text-dark">${I}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `}).join("");w.html(o)}function ie(e){!m||!f.length||!u[e]||(j(e),x=e,f.addClass("active").attr("aria-hidden","false"),$("body").addClass("best-seller-overlay-open"),f.trigger("focus"))}function E(){!m||!f.length||(x=null,f.removeClass("active").attr("aria-hidden","true"),$("body").removeClass("best-seller-overlay-open"))}function U(){m&&(M("weekly"),M("monthly"),v.length&&v.text("Loading best seller highlights..."),$.ajax({url:"/inventory/best-sellers",type:"GET",data:{limit:1},dataType:"json",success:function(e){u.weekly=D(e.weekly||null),u.monthly=D(e.monthly||null),B("weekly"),B("monthly"),ae(),x&&j(x)},error:function(){u.weekly=null,u.monthly=null,O("weekly"),O("monthly"),v.length&&v.text("Unable to load best seller data right now."),w.length&&w.html(`
                        <div class="col-12">
                            <div class="text-center text-danger py-5">Failed to load best seller data.</div>
                        </div>
                    `)}}))}function p(e,t){const n=parseInt($(e).text().replace(/,/g,""))||0;if(n===t){$(e).text(t.toLocaleString());return}$({Counter:n}).animate({Counter:t},{duration:1500,easing:"swing",step:function(){$(e).text(Math.ceil(this.Counter).toLocaleString())},complete:function(){$(e).text(Math.ceil(t).toLocaleString())}})}C();function C(){$.get("/inventory/data-to-display",function(e){if(e){p("#toRecieveCount",e.to_receive||0),p("#totalStocksCount",e.total_stocks||0),p("#lowStocksCount",e.low_stocks||0),p("#outOfStockCount",e.out_of_stock||0),p("#expiredCount",e.expired||0),p("#expiringSoonCount",e.expiring_soon||0),p("#activeItemsCount",e.active_items||0);const t=parseFloat(e.total_value||0);$("#totalValueAmount").text("₱ "+t.toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2})),le("recentItems")}else console.error("No data received from the server.")}).fail(function(e){const t=e.responseJSON?e.responseJSON.error:"Failed to fetch dashboard counts.";console.error(t)})}const H=3,g="#invClaims",y="#invRestock";let _=[],k=[];function A(){fetch("/inventory/get-to-receive").then(e=>{if(!e.ok)throw new Error("Network response was not ok: "+e.statusText);return e.json()}).then(e=>{_=e.data||[],$(g).empty(),q(_,H)}).catch(e=>{console.error("Error fetching 'To Receive' data:",e),$(g).html('<div class="alert alert-danger">Error loading pending receipts.</div>')})}function T(){fetch("/inventory/get-to-restock").then(e=>{if(!e.ok)throw new Error("Network response was not ok: "+e.statusText);return e.json()}).then(e=>{k=e.data||[],$(y).empty(),J(k,H)}).catch(e=>{console.error("Error fetching 'To Restock' data:",e),$(y).html('<div class="alert alert-danger">Error loading pending receipts.</div>')})}function q(e,t=e.length){if($(g).empty(),!Array.isArray(e)||e.length===0){$(g).html('<div class="alert alert-light-success">No purchase orders are currently ready for receiving.</div>');return}const n=e.slice(0,t),r=e.length>t;if(n.forEach(a=>{const s=a.invoice_id,o=a.id,i=a.supplier_name||"N/A";let l=0;a.purchase_orders&&Array.isArray(a.purchase_orders)&&a.purchase_orders.forEach(d=>{d.details&&Array.isArray(d.details)&&(l+=d.details.length)});const c=`
                <div class="alert alert-light-success alert-dismissible fade show" role="alert">
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8 col-md-8 justify-content-start d-flex">
                            <div class="d-block">
                                <h6>PO NO: ${o}</h6>
                                <p class="mb-1 ">From: ${i}</p>
                                <p class="mb-0 ">Total Item(s): ${l}</p>
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 col-md-4 p-0 justify-content-end align-items-center d-flex">
                            <a href="#" class="btn icon btn-sm btn-success btn-receive bs-tooltip me-2" data-id="${s}" data-req-id="${o}" title="Receive Inventory">
                                <i class="fa-solid fa-box-open"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;$(g).append(c)}),r){const s=`
                <div class="text-center mt-3" id="viewAllContainer">
                    <a href="#" class="btn btn-sm btn-outline-info btn-show-all">
                        View All ${e.length-t} More Requests
                    </a>
                </div>
            `;$(g).append(s)}}function J(e,t=e.length){if($(y).empty(),!Array.isArray(e)||e.length===0){$(y).html('<div class="alert alert-light-warning">No items are currently low in stocks.</div>');return}const n=e.slice(0,t),r=e.length>t;if(n.forEach(a=>{const s=a.item_id,o=a.sku,i=a.category||"N/A",l=a.item_name||"N/A",c=a.unit_price,d=a.original_unit||a.unit;let I='<div class="alert alert-light-warning alert-dismissible fade show" role="alert">';const G=Number(a.original_stock_level??a.stock_level??0),z=a.original_stock_display||a.stock_display||(a.original_stock_level_formatted??a.stock_level_formatted?`${a.original_stock_level_formatted??a.stock_level_formatted}${d?" "+d:""}`:Number(G||0).toLocaleString("en-PH",{minimumFractionDigits:2,maximumFractionDigits:2})+(d?" "+d:""));let K=`<p class="mb-0 ">Current Stock(s): ${z}</p>`;G===0&&(I='<div class="alert alert-light-danger alert-dismissible fade show" role="alert">',K=`<p class="mb-0 ">${z}</p>`);const oe=`
                ${I}
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8 col-md-8 justify-content-start d-flex">
                            <div class="d-block">
                                <h6>${o}</h6>
                                <p class="mb-0 ">Category: ${i}</p>
                                <p class="mb-0 ">Item Name: ${l}</p>
                                ${K}
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 col-md-4 p-0 justify-content-end align-items-center d-flex">
                            <a href="#" class="btn icon btn-sm btn-success btn-restock bs-tooltip me-2"
                            data-id="${o}" data-item-id="${s}"
                            data-item-name="${l}" data-unit-price="${c}"
                            data-unit="${d}" title="Restock Inventory">
                                <i class="fa-solid fa-receipt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;$(y).append(oe)}),r){const s=`
                <div class="text-center mt-3" id="viewAll">
                    <a href="#" class="btn btn-sm btn-outline-info btn-show-restock">
                        View All ${e.length-t} More Requests
                    </a>
                </div>
            `;$(y).append(s)}}$(document).on("click",".btn-receive",function(){const e=$(this).data("id"),t=$(this).data("req-id");$("#LoadingScreen").fadeIn(200),$.get(`/inventory/items-to-receive/get-invoice/${e}`,function(n){if(n.data){const r=n.data;se(r,t)}else alert("Error: Invoice not found.")}).fail(function(n){const r=n.responseJSON?n.responseJSON.error:"Failed to load purchase invoice details.";alert(r)}).always(function(){$("#LoadingScreen").fadeOut(200)})}),$(document).on("click",".btn-restock",function(){const e=$(this).data("id"),t=$(this).data("item-id"),n=$(this).data("item-name"),r=$(this).data("unit-price"),a=$(this).data("unit");$("#req_item_id").val(t),$("#req_sku").val(e),$("#req_item_name").text("Item Name: "+n),$("#req_unit_price").text("Unit Price: ₱"+r),$("#req_unit").text("Unit: "+a),$("#req_unit_price").attr("data-price",r),$("#stockRequest").modal("show")});function W(){const e=$("#req_unit_price"),t=parseFloat(e.data("price"))||0,n=$("#qnty"),r=parseInt(n.val())||0,a=$("#total_price");if(t<=0){a.text("");return}const o=(t*r).toLocaleString("en-PH",{style:"currency",currency:"PHP",minimumFractionDigits:2});a.text("Total Price: "+o)}$("#cancelStockReq").click(function(e){e.preventDefault(),V()}),$("#qnty").on("input",W),W(),$("#submit-req-btn").click(function(e){e.preventDefault();let t=!0;if($("#restockReqForm").find("input, number").each(function(){const r=$(this),a=r.val();r.prop("required")&&(!a||!a.trim())?(r.addClass("is-invalid"),t=!1):r.removeClass("is-invalid")}),t){let r=new FormData($("#restockReqForm")[0]);Swal.fire({title:"Confirm Request",text:"You are about to request a restock for this item.",icon:"warning",showCancelButton:!0,confirmButtonText:"Submit",cancelButtonText:"Cancel",confirmButtonColor:"#3085d6",cancelButtonColor:"#dc3545"}).then(a=>{a.isConfirmed&&($("#LoadingScreen").fadeIn(200),$.ajax({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},url:"/inventory/send-restock-request",type:"POST",data:r,processData:!1,contentType:!1,success:function(s){$("#LoadingScreen").fadeOut(200),$("#restockReqForm").trigger("reset"),Toast.fire({title:"Success!",text:s.message,icon:"success"}),C(),A(),T(),V(),$("#stockRequest").modal("hide")},error:function(s){var o;if($("#LoadingScreen").fadeOut(200),(o=s.responseJSON)!=null&&o.errors){let i=Object.values(s.responseJSON.errors).flat().join(`
`);Toast.fire("Validation Error",i,"error")}else Toast.fire("Error","An unexpected error occurred.","error")}}))})}});function V(){$("#req_item_id").val(""),$("#req_sku").val(""),$("#qnty").val(""),$("#req_item_name").text(""),$("#req_unit_price").text(""),$("#total_price").text(""),$("#req_unit_price").removeAttr("data-price")}$(document).on("click","#receiveItem",function(){const e=$(this).data("id");Swal.fire({title:"Confirm Receipt",text:"Are you sure you want to mark this purchase order as received? This action cannot be undone.",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, Receive",cancelButtonText:"Cancel",confirmButtonColor:"#3085d6",cancelButtonColor:"#dc3545"}).then(t=>{t.isConfirmed&&($("#LoadingScreen").fadeIn(200),$.post(`/inventory/items-to-receive/receive-inventory/${e}`,{_token:$('meta[name="csrf-token"]').attr("content")},function(n){if(n.success)Toast.fire({title:"Success!",text:n.message,icon:"success"}),C(),A(),T(),$("#viewInvoice").modal("hide");else{const r=n.error||"Failed to mark items as received.";Swal.fire("Error",r,"error")}}).fail(function(n){const r=n.responseJSON?n.responseJSON.error:"Failed to mark items as received.";Swal.fire("Error",r,"error")}).always(function(){$("#LoadingScreen").fadeOut(200)}))})}),$(document).on("click",".btn-show-all",function(e){e.preventDefault(),_.length>0?q(_,_.length):console.error("Full request list is not available.")}),$(document).on("click",".btn-show-restock",function(e){e.preventDefault(),k.length>0?J(k,k.length):console.error("Full request list is not available.")}),m&&($(document).on("click",".best-seller-summary-card",function(){const e=$(this);if(e.attr("data-has-data")!=="true")return;const t=e.data("mode");ie(t)}),f.on("click",function(e){$(e.target).is(f)&&E()}),$(document).on("click","[data-overlay-dismiss]",function(e){e.preventDefault(),E()}),$(document).on("keydown",function(e){e.key==="Escape"&&f.hasClass("active")&&E()})),A(),T(),m&&U(),$("#btn-refresh-recent-items").on("click",function(){C(),A(),T(),m&&U(),Y.ajax.reload(null,!1)});function se(e,t){const n=new Set;let r="",a=0;e.purchase_orders&&e.purchase_orders.length>0&&e.purchase_orders.forEach(i=>{n.add(i.purchase_order_id||"N/A");const l=i.details||[];l.length>0&&l.forEach(c=>{a++,r+=`
                    <tr>
                        <td>${a}</td>
                        <td>${c.item_name||"N/A"}</td>
                        <td>${c.item_unit_name||"N/A"}</td>
                        <td class="text-end">₱${parseFloat(c.unit_price||0).toFixed(2)}</td>
                        <td class="text-end">${c.quantity||0} ${c.item_unit||"N/A"}</td>
                        <td class="text-end">₱${parseFloat(c.total_amount||0).toFixed(2)}</td>
                    </tr>
                    `})});const s=Array.from(n).join(", ");r===""&&(r='<tr><td colspan="7" class="text-center">No item details were found across all Purchase Orders.</td></tr>');const o=`
    <div class="row mb-4 p-3">
        <!-- Invoice Header -->
        <div class="col-md-6">
            <p class="mb-0">Purchase Order #: ${s||"N/A"}</p>
            <p class="mb-0">Supplier: ${e.supplier_name}</p>
            <p class="mb-0">Date Approved: ${e.date_approved||"N/A"}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <p class="mb-0">Invoice #: ${e.id||"N/A"}</p>
            <p class="mb-0">Delivery #: ${e.delivery_no||"N/A"}</p>
            <p class="mb-0">Delivered On: ${e.date_received||"N/A"}</p>
        </div>
    </div>

    <hr class="mt-0">

    <div class="px-3">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover dataTable no-footer">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    ${r}
                    <tr>
                        <td colspan="5" class="text-end"><strong>Total Amount:</strong></td>
                        <td class="text-end"><strong>₱${parseFloat(e.total_amount||0).toFixed(2)}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <style>
    .table-sm td,
    .table-sm th {
        padding: 0.4rem 0.6rem;
        font-size: 0.875rem;
    }
    </style>
    `;$("#receiveItem").data("id",t),$("#LoadingScreen").fadeOut(200),$("#viewInvoice .modal-body").html(o),$("#viewInvoice").modal("show")}});
