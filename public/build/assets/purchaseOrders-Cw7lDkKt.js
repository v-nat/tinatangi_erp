import{b,a as _,p as S}from"./modal_builders_scm-_RUH4sv8.js";import{r as p}from"./reloadTable-EpUDf5t5.js";import{f}from"./formatDateAndTime-BEl0wwlV.js";$(document).ready(function(){let l=null,u=null;const m='<span class="badge bg-warning">Approved<br>Pending Dispatch</span>',v='<span class="badge bg-success">Accepted<br>Supplier</span>',h='<span class="badge bg-success">Redeliver<br>Supplier</span>',g=['<span class="badge bg-success">Delivered</span>','<span class="badge bg-success">Completed</span>','<span class="badge bg-warning">Partial Delivered</span>','<span class="badge bg-danger">Return</span>'],c=$("#purchaseOrderTable").DataTable({responsive:!0,scrollX:!1,processing:!0,serverSide:!1,ajax:{url:"/procurement/purchases/get-list",type:"GET",dataSrc:"data"},columns:[{data:null,render:function(t,s,e,r){return r.row+1},className:"text-center",width:"45px"},{data:"purchase_orders",title:"Order No.",className:"dt-left",render:function(t){return t&&t.length>0?t[0].purchase_order_id:"N/A"}},{data:"type",className:"dt-left"},{data:"purchase_orders",title:"Order Date",className:"dt-left",render:{_:function(t,s,e){let r=t&&t.length>0?t[0].order_date:null;return s==="display"?r?f(r):"N/A":r}}},{data:"purchase_orders",title:"Supplier",className:"dt-left",render:function(t){return t&&t.length>0?t[0].supplier_name:"N/A"}},{data:"purchase_orders",title:"Delivery Date",className:"dt-left",render:{_:function(t,s,e){let r=t&&t.length>0?t[0].delivery_date:null;return s==="display"?r?f(r):"N/A":r}}},{data:"requested_by_id",className:"dt-left"},{data:"remarks",className:"dt-left"},{data:"status",className:"text-center",width:"150px"},{data:"id",render:function(t,s,e){const r=e.invoice_id||"";let a="";return e.status===m?a="pending-dispatch":e.status===v?a="accepted-supplier":e.status===h?a="redeliver-supplier":g.includes(e.status)&&(a="has-invoice"),`
                    <div class="action-btns">
                        <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip"
                        data-id="${t}"
                        data-invoice-id="${r}"
                        data-status-key="${a}"
                        title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                    `},className:"text-center",width:"150px"},{data:"invoice_id",visible:!1}],initComplete:function(){const t=this.api().column(4),s=$("#supplier_filter"),e=new Set;t.data().each(function(o,y){let d="N/A";o&&o.length>0&&(d=o[0].supplier_name),d&&d!=="N/A"&&e.add(d)}),Array.from(e).sort().forEach(function(o){s.append($("<option></option>").attr("value",o).text(o))});const a=this.api().column(8),n=$("#status_filter"),i=new Set;a.data().unique().each(function(o,y){if(o){let d=$(o).text();d||(d=o),i.add(d)}}),Array.from(i).sort().forEach(function(o){n.append($("<option></option>").attr("value",o).text(o))})}});$("#order_date_filter").on("change",function(){c.column(3).search($(this).val(),!1,!0).draw()}),$("#supplier_filter").on("change",function(){const t=$(this).val();c.column(4).search(t?"^"+t+"$":"",!0,!1).draw()}),$("#delivery_date_filter").on("change",function(){c.column(5).search($(this).val(),!1,!0).draw()}),$("#status_filter").on("change",function(){const t=$(this).val();c.column(8).search(t,!1,!1).draw()}),$("#btn-refresh-purchase-orders").on("click",function(){c.ajax.reload(null,!1)}),$(document).on("click",".btn-view",function(){const t=$(this).data("id"),s=$(this).data("invoice-id"),e=$(this).data("status-key");l=t,u=s,$("#po-process-btn, #po-receive-btn, #po-redeliver-btn, #po-invoice-btn").addClass("d-none"),e==="pending-dispatch"?$("#po-process-btn").removeClass("d-none"):e==="accepted-supplier"?($("#po-receive-btn").removeClass("d-none"),$("#po-invoice-btn").removeClass("d-none")):e==="redeliver-supplier"?$("#po-redeliver-btn").removeClass("d-none"):e==="has-invoice"&&$("#po-invoice-btn").removeClass("d-none"),$("#LoadingScreen").fadeIn(200),$.get(`/procurement/purchases/get-details/${t}`,function(r){r.data&&r.data.length>0?b(r.data[0]):Toast.fire("Error","Purchase Request not found.","error")}).fail(function(r){const a=r.responseJSON?r.responseJSON.error:"Failed to load purchase request details.";Toast.fire("Error",a,"error")}).always(function(){$("#LoadingScreen").fadeOut(200)})}),$("#po-process-btn").on("click",function(){Swal.fire({title:"Process Purchase Order?",text:"You are about to request this order to supplier.",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Confirm!"}).then(t=>{t.isConfirmed&&($("#viewPO").modal("hide"),$("#LoadingScreen").fadeIn(200),$.ajax({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},url:`/procurement/purchases/order/${l}/21`,type:"PUT",data:null,processData:!1,contentType:!1,success:function(s){$("#LoadingScreen").fadeOut(200),p("purchaseOrderTable"),Toast.fire({text:s.message,icon:"success"})},error:function(s){var e;if($("#LoadingScreen").fadeOut(200),(e=s.responseJSON)!=null&&e.errors){let r=Object.values(s.responseJSON.errors).flat().join(`
`);Toast.fire("Validation Error",r,"error")}else Toast.fire("Error","An unexpected error occurred.","error")}}))})}),$("#po-invoice-btn").on("click",function(){$("#viewPO").modal("hide"),$("#LoadingScreen").fadeIn(200),$.get(`/procurement/purchases/get-invoice/${u}`,function(t){t.data?_(t.data):Toast.fire("Error","Invoice not found.","error")}).fail(function(t){const s=t.responseJSON?t.responseJSON.error:"Failed to load purchase invoice details.";Toast.fire("Error",s,"error")}).always(function(){$("#LoadingScreen").fadeOut(200)})}),$("#po-receive-btn").on("click",function(){const t=l;$("#viewPO").modal("hide"),$("#LoadingScreen").fadeIn(200),$("#receiveOrderForm")[0].reset(),$("#receiveItemsList").html('<tr><td colspan="5" class="text-center">Loading items...</td></tr>'),$.get(`/procurement/purchases/get-delivery-details/${t}`,function(s){const e=s.data,r=$("#receiveItemsList");if(r.empty(),$("#receive_pr_id").val(t),!e||e.length===0){r.html('<tr><td colspan="5" class="text-center text-danger">No items found for this order.</td></tr>'),$("#LoadingScreen").fadeOut(200),$("#receiveOrderModal").modal("show");return}e.forEach((a,n)=>{const i=`
                    <tr class="item-row table-success" data-index="${n}">
                        <td class="align-middle">
                            <input type="hidden" name="items[${n}][pod_id]" value="${a.pod_id}">
                            <strong>${a.item_name}</strong>
                        </td>
                        <td class="text-center align-middle">
                            ${a.quantity_ordered} ${a.item_unit}
                        </td>
                        <td class="text-center align-middle">
                            <input type="number"
                                class="form-control form-control-sm text-center item-received-qty"
                                name="items[${n}][received_qty]"
                                min="0"
                                max="${a.quantity_ordered}"
                                value="${a.quantity_ordered}"
                                data-index="${n}"
                                data-max="${a.quantity_ordered}">
                        </td>
                        <td class="text-center align-middle fw-bold">
                            <span id="return_qty_${n}">0</span> ${a.item_unit}
                        </td>
                        <td class="align-middle">
                            <div class="return-fields" id="return_fields_${n}" style="display:none;">
                                <textarea class="form-control mb-2" name="items[${n}][return_reason]"
                                    placeholder="Reason for return..." disabled></textarea>
                                <input type="file" class="form-control" name="items[${n}][return_photo]"
                                    accept="image/*" disabled>
                            </div>
                        </td>
                    </tr>
                `;r.append(i)}),$("#LoadingScreen").fadeOut(200),$("#receiveOrderModal").modal("show")}).fail(function(s){var e;$("#LoadingScreen").fadeOut(200),Toast.fire("Error",((e=s.responseJSON)==null?void 0:e.error)||"Failed to load item details for inspection.","error")})}),$("#po-redeliver-btn").on("click",function(){const t=l;$("#viewPO").modal("hide"),$("#LoadingScreen").fadeIn(200),$("#receiveRedeliveryForm")[0].reset(),$("#redeliveryItemsList").html('<tr><td colspan="6" class="text-center">Loading items...</td></tr>'),$.get(`/procurement/purchases/get-redelivery-details/${t}`,function(s){const e=s.data,r=$("#redeliveryItemsList");if(r.empty(),$("#receive_redelivery_pr_id").val(t),!e||e.length===0){r.html('<tr><td colspan="6" class="text-center text-danger">No items found for redelivery.</td></tr>'),$("#LoadingScreen").fadeOut(200),$("#receiveRedeliveryModal").modal("show");return}e.forEach((a,n)=>{const i=`
                    <tr class="item-row-redelivery table-success" data-index="${n}">
                        <td class="align-middle">
                            <input type="hidden" name="items[${n}][pod_id]" value="${a.pod_id}">
                            <strong>${a.item_name}</strong>
                        </td>
                        <td class="text-center align-middle">
                            ${a.quantity_ordered} ${a.item_unit}
                        </td>
                        <td class="text-center align-middle fw-bold text-primary">
                            ${a.backorder_qnty} ${a.item_unit}
                        </td>
                        <td class="text-center align-middle">
                            <input type="number"
                                class="form-control form-control-sm text-center item-redelivery-received-qty"
                                name="items[${n}][received_qty]"
                                min="0"
                                max="${a.backorder_qnty}"
                                value="${a.backorder_qnty}"
                                data-index="${n}"
                                data-max="${a.backorder_qnty}">
                        </td>
                        <td class="text-center align-middle fw-bold">
                            <span id="redeliver_return_qty_${n}">0</span> ${a.item_unit}
                        </td>
                        <td class="align-middle">
                            <div class="return-fields-redelivery" id="return_fields_redeliver_${n}" style="display:none;">
                                <textarea class="form-control mb-2" name="items[${n}][return_reason]"
                                    placeholder="Reason for returning again..." disabled></textarea>
                                <input type="file" class="form-control" name="items[${n}][return_photo]"
                                    accept="image/*" disabled>
                            </div>
                        </td>
                    </tr>
                `;r.append(i)}),$("#LoadingScreen").fadeOut(200),$("#receiveRedeliveryModal").modal("show")}).fail(function(s){var e;$("#LoadingScreen").fadeOut(200),Toast.fire("Error",((e=s.responseJSON)==null?void 0:e.error)||"Failed to load redelivery item details.","error")})}),$("#viewPO").on("hidden.bs.modal",function(){l=null,u=null,$("#po-process-btn, #po-receive-btn, #po-redeliver-btn, #po-invoice-btn").addClass("d-none")}),$(document).on("click","#print",function(){S()}),$(document).on("input change",".item-received-qty",function(){const t=$(this).data("index"),s=parseInt($(this).data("max"));let e=parseInt($(this).val())||0;e<0&&(e=0),e>s&&(e=s),$(this).val(e);const r=$(`#return_fields_${t}`),a=$(`#return_qty_${t}`),n=r.find("textarea, input[type=file]"),i=s-e;a.text(i),i>0?(r.show(),n.prop("disabled",!1),r.find("textarea").prop("required",!0),$(this).closest("tr").addClass("table-warning").removeClass("table-success")):(r.hide(),n.prop("disabled",!0).prop("required",!1),$(this).closest("tr").removeClass("table-warning").addClass("table-success"))}),$(document).on("input change",".item-redelivery-received-qty",function(){const t=$(this).data("index"),s=parseInt($(this).data("max"));let e=parseInt($(this).val())||0;e<0&&(e=0),e>s&&(e=s),$(this).val(e);const r=$(`#return_fields_redeliver_${t}`),a=$(`#redeliver_return_qty_${t}`),n=r.find("textarea, input[type=file]"),i=s-e;a.text(i),i>0?(r.show(),n.prop("disabled",!1),r.find("textarea").prop("required",!0),$(this).closest("tr").addClass("table-warning").removeClass("table-success")):(r.hide(),n.prop("disabled",!0).prop("required",!1),$(this).closest("tr").removeClass("table-warning").addClass("table-success"))}),$("#receiveOrderForm").on("submit",function(t){t.preventDefault(),$("#LoadingScreen").fadeIn(200);let s=!0;if($("#receiveItemsList .item-row").each(function(){const e=parseInt($(this).find(".item-received-qty").data("max")),r=parseInt($(this).find(".item-received-qty").val())||0,a=e-r,n=$(this).find("textarea");a>0&&!n.val().trim()?(n.addClass("is-invalid"),s=!1):n.removeClass("is-invalid")}),!s){$("#LoadingScreen").fadeOut(200),Toast.fire("Error","Please provide a return reason for all items with returned quantity.","error");return}$.ajax({url:"/procurement/purchases/receive-delivery",type:"POST",data:new FormData(this),processData:!1,contentType:!1,headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(e){$("#LoadingScreen").fadeOut(200),$("#receiveOrderModal").modal("hide"),p("purchaseOrderTable"),Toast.fire({text:e.message,icon:"success"})},error:function(e){var r;if($("#LoadingScreen").fadeOut(200),e.status===422){let a="<strong>Validation Failed:</strong><ul class='text-start'>";$.each(e.responseJSON.errors,(n,i)=>{a+=`<li>${i[0]}</li>`}),a+="</ul>",Toast.fire({title:"Error",html:a,icon:"error"})}else Toast.fire("Error",((r=e.responseJSON)==null?void 0:r.error)||"An unexpected error occurred.","error")}})}),$("#receiveRedeliveryForm").on("submit",function(t){t.preventDefault(),$("#LoadingScreen").fadeIn(200);let s=!0;if($("#redeliveryItemsList .item-row-redelivery").each(function(){const e=parseInt($(this).find(".item-redelivery-received-qty").data("max")),r=parseInt($(this).find(".item-redelivery-received-qty").val())||0,a=e-r,n=$(this).find("textarea");a>0&&!n.val().trim()?(n.addClass("is-invalid"),s=!1):n.removeClass("is-invalid")}),!s){$("#LoadingScreen").fadeOut(200),Toast.fire("Error","Please provide a reason for all items being returned again.","error");return}$.ajax({url:"/procurement/purchases/receive-redelivery",type:"POST",data:new FormData(this),processData:!1,contentType:!1,headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(e){$("#LoadingScreen").fadeOut(200),$("#receiveRedeliveryModal").modal("hide"),p("purchaseOrderTable"),Toast.fire({text:e.message,icon:"success"})},error:function(e){var r;if($("#LoadingScreen").fadeOut(200),e.status===422){let a="<strong>Validation Failed:</strong><ul class='text-start'>";$.each(e.responseJSON.errors,(n,i)=>{a+=`<li>${i[0]}</li>`}),a+="</ul>",Toast.fire({title:"Error",html:a,icon:"error"})}else Toast.fire("Error",((r=e.responseJSON)==null?void 0:r.error)||"An unexpected error occurred.","error")}})})});
