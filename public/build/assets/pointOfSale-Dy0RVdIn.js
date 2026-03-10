$(document).ready(function(){$(document).on("click","#exit-pos-btn",function(t){t.preventDefault(),Swal.fire({title:"Are you sure?",text:"You are about End POS Session.",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Exit"}).then(o=>{o.isConfirmed&&($("#LoadingScreen").fadeIn(200),window.location.href="/operations")})});const A=$("#v-pills-tab"),N=$("#v-pills-tabContent"),Y='<div class="text-center py-3 text-muted">Loading categories...</div>',K='<div class="text-center py-5 text-muted">Select a category to view products.</div>',Z='<div class="text-center py-3 text-danger">Failed to load categories.</div>';let C=[];const R={};let L=null,O="default",Q=new Map;function V(t,o){t&&(R[t]=Array.isArray(o)?o.map(e=>Object.assign({},e)):[])}function B(t){return!t||!R[t]?[]:R[t].map(o=>Object.assign({},o))}function tt(t){if(!Array.isArray(t))return[];const o=t.map(e=>Object.assign({},e));return O==="alphabetical"?o.sort((e,a)=>{const r=(e.name||"").toString().toLowerCase(),n=(a.name||"").toString().toLowerCase();return r<n?-1:r>n?1:0}):O==="servings"?o.sort((e,a)=>{const r=parseInt(e.available_servings??e.servings??0,10)||0;return(parseInt(a.available_servings??a.servings??0,10)||0)-r}):o}function D(t,o){const e=$(o);if(!Array.isArray(t)||t.length===0){e.html(`
                <div class="col-12 text-center my-5 p-5">
                    <h3 class="text-muted">No available products in this category.</h3>
                    <p class="text-muted">Available products will appear here automatically.</p>
                </div>`),E();return}const a=tt(t),r=[];a.forEach(n=>{const s=n.image;let d;const u=n.name||"",f=u.toLowerCase();s&&s!=="N/A"?d="/storage/app/public/"+s:d=DEFAULT_PRODUCT_IMAGE;const p=n.available_servings??n.servings??0,g=parseInt(p,10),x=Number.isNaN(g)?0:g;let h="";x>0?h=`<span class="position-absolute top-0 end-0 bg-primary text-white p-1 px-2 rounded-pill" style="font-size: 0.8rem; margin: 5px;">${x} servings</span>`:h=`
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(220, 53, 69, 0.7); border-radius: inherit;">
                        <h5 class="text-white fw-bold">Out of Stock</h5>
                    </div>
                `;const w=Q.get(n.id),_=w!==void 0?`<span class="position-absolute top-0 start-0 bg-warning text-dark px-2 rounded-pill" style="font-size: 0.7rem; font-weight: 600; margin: 5px; z-index: 2;">#${w} &#9733; Best Seller</span>`:"";r.push(`
                <div class="col" data-id="${n.id}" data-available-servings="${x}" data-name="${f.replace(/"/g,"&quot;")}" ${x<=0?'data-disabled="true"':""}>
                    <div class="card shadow h-100 product-card-fixed-size d-flex p-2 m-2 ${x<=0?"border-danger":""}">
                        ${h}
                        ${_}
                        <img src="${d}" class="card-img-top img-fluid prod-img" alt="Product Image">
                        <div class="card-body p-2 flex-grow-1">
                            <h6 class="card-title mb-1 prod-name">${u}</h6>
                            <h6 class="text-success mb-0 prod-price">₱${parseFloat(n.base_price||0).toFixed(2)}</h6>
                        </div>
                    </div>
                </div>
            `)}),e.html(r.join("")),E()}function J(){if(!L)return;const t=C.find(a=>a.slug===L);if(!t)return;const o=`#${t.productsContainerId}`,e=B(t.slug);D(e,o)}function U(t){return t&&t.toString().toLowerCase().trim().replace(/[\s\W-]+/g,"-").replace(/^-+|-+$/g,"")||"category"}function et(t){if(!Array.isArray(t)||t.length===0){A.html('<div class="text-center py-3 text-muted">No categories available.</div>'),N.html('<div class="text-center py-5 text-muted">No products to display.</div>');return}const o=[],e=[];C=t.map((a,r)=>{const n=a.slug||U(a.name||"category"),s=a.isAll===!0||n==="all"||(a.name||"").toLowerCase()==="all",d=`v-pills-${n}-tab`,u=`v-pills-${n}`,f=`${n}Products`,p=r===0,g=Array.isArray(a.prefetchedProducts)?a.prefetchedProducts:null;return o.push(`<a class="nav-link ${p?"active":""}" id="${d}" data-bs-toggle="pill" href="#${u}" role="tab" aria-controls="${u}" aria-selected="${p}" data-category-index="${r}">${a.name}</a>`),e.push(`<div class="tab-pane fade ${p?"show active":""} py-4" id="${u}" role="tabpanel" aria-labelledby="${d}">
                    <div id="${f}" class="row row-cols-4 g-2"></div>
                </div>`),{id:a.id??null,name:a.name,slug:n,isAll:s,navId:d,paneId:u,productsContainerId:f,prefetchedProducts:g}}),A.html(o.join("")),N.html(e.join(""))}function M(t){if(!t)return;L=t.slug;const o=`#${t.productsContainerId}`;Array.isArray(t.prefetchedProducts)&&t.prefetchedProducts.length>0&&(V(t.slug,t.prefetchedProducts),t.prefetchedProducts=null);const e=B(t.slug);if(e.length>0){D(e,o);return}let a="/operations/pos/products";t.isAll||(a+=`?category=${encodeURIComponent(t.name)}`),at(a,o,t)}function X(){A.html(Y),N.html(K),$.get("/operations/pos/categories",function(t){const o=Array.isArray(t.data)?t.data:[],e=[{id:null,name:"All",slug:"all",isAll:!0},...o.map(a=>({id:a.id??null,name:a.name??"Unnamed",slug:a.slug??U(a.name??"category")}))];$.get("/operations/pos/monthly-best-sellers",{limit:5}).done(function(a){const r=Array.isArray(a.data)?a.data:[];Q=new Map(r.map((n,s)=>[n.id,s+1]))}).fail(function(){console.warn("Warning: Unable to load monthly best sellers for POS.")}).always(function(){et(e),C.length>0&&M(C[0])})}).fail(function(){A.html(Z),N.html('<div class="text-center py-5 text-danger">Unable to load products.</div>')})}function E(){const t=($("#product-search-input").val()||"").toLowerCase().trim(),o=$("#v-pills-tabContent .tab-pane.show.active");if(o.length===0)return;const e=o.find(".row.row-cols-auto.g-3");if(e.length===0)return;const a=e.children(".col[data-name]"),r="no-product-search-results";if(e.find(`.${r}`).remove(),a.length===0)return;if(!t){a.removeClass("d-none");return}let n=0;if(a.each(function(){const s=$(this);(s.data("name")||"").toString().includes(t)?(s.removeClass("d-none"),n+=1):s.addClass("d-none")}),n===0){const s=$("<div>").text(t).html(),d=`
                <div class="col-12 text-center my-5 p-5 text-muted ${r}">
                    <h5>No products match "${s}".</h5>
                    <p>Try adjusting your search.</p>
                </div>`;e.append(d)}}function at(t,o,e){$(o).html(`
             <div class="col-12 text-center my-5 p-5 d-flex flex-column align-items-center">
                <div class="spinner-border" style="width: 3rem; height: 3rem"
                    role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 class="text-muted mt-2">Loading...</h6>
            </div>
            `),$.get(t,function(r){if(r.data&&Array.isArray(r.data)){const n=r.data,s=e&&e.slug?e.slug:null;if(s){V(s,n);const d=B(s);D(d,o)}else D(n,o)}else $(o).html(`
                <div class="col-12 text-center mt-5">
                    <p class="text-danger">Error: Could not load products.</p>
                </div>`),console.error("Error: products data not found or is not an array.")}).fail(function(r){const n=r.responseJSON?r.responseJSON.error:"Failed to load products.";console.error("AJAX Error:",n,r);const s=`<div class="col-12 text-center mt-5"><p class="text-danger">${n}</p></div>`;$(o).html(s)}).always(function(){$("#LoadingScreen").fadeOut(200),E()})}X(),$(document).on("shown.bs.tab","#v-pills-tab a[data-category-index]",function(){const t=parseInt($(this).data("category-index"),10);Number.isNaN(t)||!C[t]||M(C[t])}),$(document).on("input","#product-search-input",function(){E()}),$(document).on("change","#pos-sort-select",function(){const t=$(this).val();t==="alphabetical"||t==="servings"?O=t:O="default",J()}),$(document).on("click",".col",function(){const t=$(this);if(t.data("disabled")===!0){Toast.fire({icon:"error",title:"Out of Stock",text:"This product is currently unavailable.",timer:1500});return}const o=t.data("id"),e=parseInt(t.data("available-servings"),10)||0,a=$(`#orderList .prod-name[data-id="${o}"]`).closest(".order-item-row"),r=a.length&&parseInt(a.find(".qnty").text().trim(),10)||0;if(e>0&&r>=e){Toast.fire({icon:"warning",title:"Servings Exhausted",text:"No more servings available for this product.",timer:1800});return}const s=t.find(".prod-name").text().trim(),u=t.find(".prod-price").text().trim(),f=parseFloat(u.replace("₱","").trim());$("#_item_id").val(o),$("#_item_name").text("Product Name: "+s),$("#_base_price").text("Price: ₱"+f),$("#_base_price").data("price",f);const p=$("#quantity"),g=e>0?Math.max(e-r,0):0;$("#addOrder").data("available-servings",e).data("current-quantity",r),g>0?(p.attr("max",g),(!p.val()||parseInt(p.val(),10)>g)&&p.val(1)):(p.removeAttr("max"),p.val(1)),G(),$("#addItemOrder").modal("show")});function G(){const t=$("#_base_price"),o=parseFloat(t.data("price"))||0,e=$("#quantity"),a=parseInt(e.val())||0,r=$("#total_price");if(o<=0){r.text("");return}const s=(o*a).toLocaleString("en-PH",{style:"currency",currency:"PHP",minimumFractionDigits:2});r.text("Total Price: "+s)}$("#quantity").on("input",G),G(),$("#cancelAddOrder").click(function(t){t.preventDefault(),W()});function W(){$("#_item_id").val(""),$("#quantity").val(""),$("#_item_name").text(""),$("#_base_price").text(""),$("#total_price").text(""),$("#_base_price").removeAttr("data-price"),$("#quantity").removeAttr("max"),$("#addOrder").removeData("available-servings").removeData("current-quantity")}$("#addOrderBtn").click(function(t){t.preventDefault();let o=!0;if($("#addOrder").find("input, number").each(function(){const a=$(this),r=a.val();a.prop("required")&&(!r||!r.trim())?(a.addClass("is-invalid"),o=!1):a.removeClass("is-invalid")}),o){$("#LoadingScreen").fadeIn(200);const a=$("#_item_id").val(),r=parseFloat($("#_base_price").data("price"))||0,n=$("#quantity"),s=parseInt(n.val())||1,d=parseInt($("#addOrder").data("available-servings"),10)||0,f=(parseInt($("#addOrder").data("current-quantity"),10)||0)+s;if(d>0&&f>d){$("#LoadingScreen").fadeOut(200),Toast.fire({icon:"error",title:"Not enough servings",text:"Quantity exceeds available servings."});return}const g=$("#_item_name").text().trim().replace("Product Name: ","").trim(),x=$("#total_price").text().trim(),h=parseFloat(x.replace(/[^0-9.]/g,"").trim())||0,w=s>0?h/s:r,_=$(`#orderList .prod-name[data-id="${a}"]`).closest(".order-item-row");let S=_.length>0;const k=$("#order-total-amount"),H=(parseFloat(k.text().replace(/[^0-9.]/g,""))||0)+h;if(k.text("₱ "+parseFloat(H).toFixed(2)),S){const I=_.find(".qnty"),q=_.find(".prod-price"),i=_.find(".unit-price");let c=parseInt(I.text().trim())||0,l=q.text().trim(),m=parseFloat(l.replace(/[^0-9.]/g,""))||0;const v=c+s;if(d>0&&v>d){$("#LoadingScreen").fadeOut(200),Toast.fire({icon:"error",title:"Not enough servings",text:"Quantity exceeds available servings."});return}const b=m+h;I.text(v),q.text("₱"+parseFloat(b).toFixed(2)),i.text("₱"+parseFloat(w).toFixed(2)),_.addClass("order-item-row").attr("data-available-servings",d).data("available-servings",d)}else{$("#orderList").find("#order-placeholder").remove();const I=`
                <div class="d-flex align-items-center py-2 border-bottom order-item-row" data-product-id="${a}" data-available-servings="${d}">
                    <div class="flex-grow-1 me-3">
                        <h6 class="mb-0 text-primary text-muted prod-name" data-id="${a}">${g}</h6>
                        <small class="text-muted d-block unit-price">₱${r.toFixed(2)} each</small>
                        <small class="text-secondary prod-price">₱${parseFloat(h||0).toFixed(2)}</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="width: 110px;">
                        <a href="#" class="btn btn-sm btn-danger p-1 dec-qty-btn">
                            <i class="fa-solid fa-minus"></i>
                        </a>
                        <h6 class="mb-0 mx-2 qnty">${s}</h6>
                        <a href="#" class="btn btn-sm btn-primary p-1 inc-qty-btn">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                </div>
            `;$("#orderList").append(I)}$("#LoadingScreen").fadeOut(200),W(),$("#addItemOrder").modal("hide")}}),$(document).on("click",".dec-qty-btn",function(){const o=$(this).closest(".order-item-row"),e=o.find(".qnty"),a=o.find(".prod-price"),r=$("#order-total-amount");let n=parseInt(e.text().trim())||0,s=a.text().trim(),d=parseFloat(s.replace(/[^0-9.]/g,""))||0;const u=parseFloat(r.text().replace(/[^0-9.]/g,""))||0,f=n>0?d/n:0;if(n>1){const p=u-f,g=d-f;e.text(n-1),a.text("₱ "+parseFloat(g).toFixed(2)),r.text("₱ "+parseFloat(p).toFixed(2))}else if(n===1){const p=u-d;o.remove(),r.text("₱ "+parseFloat(p).toFixed(2))}}),$(document).on("click",".inc-qty-btn",function(){const o=$(this).closest(".order-item-row"),e=o.find(".qnty"),a=o.find(".prod-price"),r=$("#order-total-amount");let n=parseInt(e.text().trim())||0;const s=parseInt(o.data("available-servings"),10)||0;if(s>0&&n>=s){Toast.fire({icon:"warning",title:"Limit reached",text:"No more servings available for this product.",timer:1800});return}let d=a.text().trim(),u=parseFloat(d.replace(/[^0-9.]/g,""))||0;const f=parseFloat(r.text().replace(/[^0-9.]/g,""))||0,p=n>0?u/n:0;if(p>0){const g=f+p,x=u+p;e.text(n+1),a.text("₱ "+parseFloat(x).toFixed(2)),r.text("₱ "+parseFloat(g).toFixed(2))}}),$(function(){$(document).on("click",".showTransactionsModal",function(e){$("#orderTransactions").modal("show"),t()});function t(){const e="#posOrdersTransactions";$.fn.DataTable.isDataTable(e)&&$(e).DataTable().destroy(),$(e).DataTable({processing:!0,serverSide:!1,ajax:{url:"/operations/pos/recent-orders",type:"GET",dataSrc:"data",error:function(a,r,n){console.error("DataTables AJAX Error:",a.responseText),$(e).find("tbody").html('<tr><td colspan="10" class="text-center text-danger">Failed to load orders.</td></tr>')}},columns:[{data:"order_id",title:"Order #"},{data:"items",title:"Items",render:function(a,r,n){return r==="display"?`<ul class="list-unstyled p-0 m-0" style="font-size: 0.85rem">${a.map(d=>`<li>${d.quantity}x ${d.product_name}</li>`).join("")}</ul>`:a}},{data:"created_at",title:"Date"},{data:"total_amount",title:"Amount",render:$.fn.dataTable.render.number(",",".",2,"₱ "),className:"dt-left font-weight-bold"},{data:"order_type",title:"Type"},{data:"payment_method",title:"Payment"},{data:"cashier_name",title:"Cashier",defaultContent:"N/A"},{data:"status",title:"Status",className:"font-weight-bold"},{data:null,title:"Actions",orderable:!1,width:"8%",render:function(a,r,n){let s="";return(n.status==='<span class="badge bg-warning">In Queue</span>'||n.status==='<span class="badge bg-info">In Prep</span>')&&(s=` <button class="btn btn-sm btn-danger void-order-btn" title="Void Order" data-order="${n.order_id}" data-id="${n.id}" data-status='${n.status}'><i class="fas fa-trash-alt"></i></button>`),`<div class="btn-group">${s}</div>`}}],order:[[2,"desc"]],language:{emptyTable:"No orders placed today.",zeroRecords:"No matching orders found."},fixedColumns:!0,scrollX:!0})}$("#posOrdersTransactions").on("click",".void-order-btn",function(){const e=$(this).data("id"),a=$(this).data("order"),r=$(this).data("status"),n=$("#posOrdersTransactions").DataTable();r==='<span class="badge bg-warning">In Queue</span>'?Swal.fire({title:"Are you sure?",text:`Do you want to void order #${a}? This action cannot be undone.`,icon:"warning",showCancelButton:!0,confirmButtonColor:"#d33",cancelButtonColor:"#3085d6",confirmButtonText:"Yes, void it!"}).then(s=>{s.isConfirmed&&o(e,n)}):r==='<span class="badge bg-info">In Prep</span>'&&Swal.fire({title:"Order in Progress!",html:`Order #${a} is already being prepared.<br>Voiding it now will incur a charge and waste materials.<br><br><b>Do you want to proceed?</b>`,icon:"error",showCancelButton:!0,confirmButtonColor:"#d33",cancelButtonColor:"#3085d6",confirmButtonText:"Yes, void with charges"}).then(s=>{s.isConfirmed&&o(e,n)})});function o(e,a){$.ajax({url:`/operations/pos/void-order/${e}`,type:"POST",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(r){Toast.fire("Voided!",r.message,"success"),a.ajax.reload()},error:function(r){const n=r.responseJSON?r.responseJSON.message:"An error occurred.";Swal.fire("Error!",n,"error")}})}}),$(function(){const t=window.POS_RECEIPT_META||{companyName:"Tinatangi Cafe",branchName:"Tinatangi Cafe - Dasmariñas",address:"Brgy 13 Jose Abad Santos Ave, Dasmariñas, Cavite 4114",vatTin:"000-000-000-000",accrNo:"000-00000000-00000",permitNo:"0000-0000-000-00000",serialNo:"000 0 000 000000",hotline:"(+63) 960 216 4109",email:"tinatangicafe@gmail.com",website:"www.tinatangi.site",vatRate:.12};let o=[],e=0,a=[],r=null,n=0,s=[],d=null,u=0;function f(){$.get("/operations/pos/active-discounts",function(i){a=i.data||[]})}f();function p(){$.get("/operations/pos/government-discount-types",function(i){s=i.data||[],w()})}p();function g(i){var c=(i.product_ids||[]).map(Number),l=i.applicable_to!=="specific",m=0;return o.forEach(function(v){(l||c.indexOf(Number(v.product_id))!==-1)&&(m+=parseFloat(v.total_price))}),i.discount_type==="percentage"?Math.round(m*(parseFloat(i.discount_value)/100)*100)/100:Math.min(parseFloat(i.discount_value),m)}function x(i){S(),r=i,n=g(i);var c=Math.max(0,e-n);$("#discount-label").text(i.title),$("#discount-amount-display").text("-₱"+n.toFixed(2)),$("#discount-panel").removeClass("d-none"),$("#discount-chooser").addClass("d-none"),$("#applied_discount_id").val(i.id),$("#applied_discount_amount").val(n.toFixed(2)),$("#modalGrandTotal").text("₱ "+c.toFixed(2));var l=parseFloat($("#cashReceivedInput").val())||0;l>=c?($("#modalChange").text("₱ "+(l-c).toFixed(2)),$("#confirmSubmitOrder").prop("disabled",!1)):($("#modalChange").text("₱ 0.00"),$("#confirmSubmitOrder").prop("disabled",!0))}function h(){r=null,n=0,$("#discount-panel").addClass("d-none"),$("#discount-chooser").addClass("d-none"),$("#applied_discount_id").val(""),$("#applied_discount_amount").val("0");var i=Math.max(0,e-u);$("#modalGrandTotal").text("₱ "+i.toFixed(2));var c=parseFloat($("#cashReceivedInput").val())||0;c>=i?($("#modalChange").text("₱ "+(c-i).toFixed(2)),$("#confirmSubmitOrder").prop("disabled",!1)):($("#modalChange").text("₱ 0.00"),$("#confirmSubmitOrder").prop("disabled",!0)),w()}function w(){var i=$("#gov-discount-buttons");i.find(".gov-disc-btn").remove(),s.forEach(function(c){i.append(`<button type="button" class="btn btn-sm btn-outline-success gov-disc-btn"
                             data-id="${c.id}" data-name="${c.name}" data-pct="${c.percentage}"
                             style="font-size:.75rem;padding:2px 8px">
                        ${c.name} (${c.percentage}%)
                    </button>`)}),s.length>0&&($("#gov-discount-section").removeClass("d-none"),$("#gov-discount-buttons").removeClass("d-none"))}function _(i){h(),d=i,u=Math.round(e*(i.percentage/100)*100)/100;var c=Math.max(0,e-u);$("#gov-discount-label").text(i.name+" Discount"),$("#gov-discount-sublabel").text(i.percentage+"% off total"),$("#gov-discount-amount-display").text("-₱"+u.toFixed(2)),$("#gov-discount-panel").removeClass("d-none"),$("#gov-discount-buttons").addClass("d-none"),$("#applied_gov_discount_type_id").val(i.id),$("#applied_gov_discount_amount").val(u.toFixed(2)),$("#modalGrandTotal").text("₱ "+c.toFixed(2));var l=parseFloat($("#cashReceivedInput").val())||0;l>=c?($("#modalChange").text("₱ "+(l-c).toFixed(2)),$("#confirmSubmitOrder").prop("disabled",!1)):($("#modalChange").text("₱ 0.00"),$("#confirmSubmitOrder").prop("disabled",!0))}function S(){d=null,u=0,$("#gov-discount-panel").addClass("d-none"),$("#applied_gov_discount_type_id").val(""),$("#applied_gov_discount_amount").val("0"),s.length>0&&$("#gov-discount-buttons").removeClass("d-none");var i=Math.max(0,e-n);$("#modalGrandTotal").text("₱ "+i.toFixed(2));var c=parseFloat($("#cashReceivedInput").val())||0;c>=i?($("#modalChange").text("₱ "+(c-i).toFixed(2)),$("#confirmSubmitOrder").prop("disabled",!1)):($("#modalChange").text("₱ 0.00"),$("#confirmSubmitOrder").prop("disabled",!0))}$(document).on("click",".gov-disc-btn",function(){var i=parseInt($(this).data("id")),c=s.find(function(l){return l.id===i});c&&_(c)}),$(document).on("click","#btn-remove-gov-discount",function(){S()});function k(){if(!o.length){h();return}var i=a.filter(function(l){if(l.applicable_to==="specific"){var m=(l.product_ids||[]).map(Number),v=o.some(function(b){return m.indexOf(Number(b.product_id))!==-1});if(!v)return!1}return!(l.min_spend&&e<parseFloat(l.min_spend))});if(i.length===1)x(i[0]);else if(i.length>1){h();var c="";i.forEach(function(l){var m=g(l);c+=`<button type="button" class="btn btn-sm btn-outline-warning text-start discount-option-btn" data-idx="${a.indexOf(l)}">
                                <strong>${l.title}</strong>
                                <span class="float-end text-success">-₱${m.toFixed(2)}</span>
                             </button>`}),$("#discount-options-list").html(c),$("#discount-chooser").removeClass("d-none")}else h()}$(document).on("click",".discount-option-btn",function(){var i=parseInt($(this).data("idx"));a[i]&&x(a[i])}),$(document).on("click","#btn-remove-discount",function(){h()}),$(document).on("click","#submit-order-btn",function(i){i.preventDefault(),o=[];let c=!0;if($("#orderList").find(".order-item-row").each(function(){const l=$(this),m=l.find(".prod-name").data("id"),v=l.find(".prod-name").text().trim(),b=parseInt(l.find(".qnty").text().trim())||0,y=l.find(".prod-price").text().trim(),P=parseFloat(y.replace(/[^0-9.]/g,""))||0,F=b>0?P/b:0;if(b===0||m===null)return c=!1,!1;o.push({product_id:m,name:v,quantity:b,unit_price:parseFloat(F).toFixed(2),total_price:parseFloat(P).toFixed(2)})}),e=parseFloat($("#order-total-amount").text().replace(/[^0-9.]/g,""))||0,c&&o.length>0){const l=$("#orderSummaryList");l.empty(),o.forEach(m=>{const v=`<div class="d-flex justify-content-between"><span>${m.quantity}x ${m.name}</span><span>₱ ${m.total_price}</span></div>`;l.append(v)}),$("#modalGrandTotal").text("₱ "+e.toFixed(2)),$("#cashReceivedInput").val(""),$("#modalChange").text("₱ 0.00"),$("#confirmSubmitOrder").prop("disabled",!0).removeClass("d-none"),$("#printReceiptBtn").addClass("d-none"),h(),S(),k(),$("#orderFinalization").modal("show")}else Toast.fire({text:"The order is empty or contains invalid items. Please add products.",icon:"warning",timer:2e3})}),$("#cashReceivedInput").on("keyup input",function(){const i=parseFloat($(this).val())||0,c=Math.max(0,e-n-u),l=i-c;i>=c?($("#modalChange").text("₱ "+l.toFixed(2)),$("#confirmSubmitOrder").prop("disabled",!1)):($("#modalChange").text("₱ 0.00"),$("#confirmSubmitOrder").prop("disabled",!0))}),$("#finalizeOrderForm").on("submit",function(i){i.preventDefault(),$("#LoadingScreen").fadeIn(200);const c=$("#order_type_input").val(),l=parseFloat($("#cashReceivedInput").val())||0,m=Math.max(0,e-n-u),v=l-m,b={order_items:o,grand_total:parseFloat(e).toFixed(2),order_type:c,cash_received:l.toFixed(2),change_due:Math.max(0,v).toFixed(2),discount_id:parseInt($("#applied_discount_id").val())||null,discount_amount:parseFloat($("#applied_discount_amount").val())||0,gov_discount_type_id:parseInt($("#applied_gov_discount_type_id").val())||null,gov_discount_amount:parseFloat($("#applied_gov_discount_amount").val())||0};$.ajax({url:"/operations/pos/submit-order",type:"POST",data:b,headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(y){Toast.fire({text:"Order completed! ID: "+y.order_id,icon:"success"}),J(),X(),f(),S();const P=$("#cashierNameDisplay").text().trim()||"N/A";q(y.order_id,l,v,P),$("#closeBtn").removeClass("d-none"),$("#printReceiptBtn").removeClass("d-none"),$("#cancelBtn").addClass("d-none"),$("#confirmSubmitOrder").addClass("d-none"),$("#orderList").empty(),$("#order-total-amount").text("₱ 0.00");const F=$(".nav-pills .nav-link.active");F.length>0?F.trigger("shown.bs.tab"):C.length>0&&M(C[0])},error:function(y){Toast.fire("Error: "+y.responseJSON.message)},complete:function(){$("#LoadingScreen").fadeOut(200)}})});function T(i){return"₱ "+Number(i||0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,",")}function H(i){return i.toLocaleDateString("en-PH",{year:"numeric",month:"2-digit",day:"2-digit"})}function I(i){return i.toLocaleTimeString("en-PH",{hour:"2-digit",minute:"2-digit"})}function q(i,c,l,m){const v=new Date,b=Math.max(o.length,1),y=n+u,P=Math.max(0,e-y),F=t.vatRate?e/(1+t.vatRate):e,nt=t.vatRate?e-F:0;var j="";n>0&&r?j="Promo: "+r.title:u>0&&d&&(j=d.name+" ("+d.percentage+"%)");const ot=y>0?`<tr>
                        <td>${j}</td>
                        <td class="text-end" style="color:#2a7a2a">-${T(y)}</td>
                   </tr>`:"",rt=o.map(z=>`
                    <tr>
                        <td>${z.quantity} ${z.name}</td>
                        <td class="text-end">${T(parseFloat(z.total_price))}</td>
                    </tr>
                `).join(""),it=`
            <style>
                #receipt-container .pos-receipt {
                    width: 320px;
                    margin: 0 auto;
                    font-family: "Courier New", Courier, monospace;
                    font-size: 13px;
                    color: #000;
                    padding: 12px 10px 24px;
                }
                #receipt-container .pos-receipt h2,
                #receipt-container .pos-receipt h3,
                #receipt-container .pos-receipt p {
                    margin: 0;
                    text-align: center;
                }
                #receipt-container .pos-receipt hr {
                    border: none;
                    border-top: 1px solid #000;
                    margin: 8px 0;
                }
                #receipt-container .pos-receipt table {
                    width: 100%;
                    border-collapse: collapse;
                }
                #receipt-container .pos-receipt td {
                    padding: 2px 0;
                }
                #receipt-container .text-end {
                    text-align: right;
                }
                #receipt-container .meta-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    row-gap: 2px;
                    column-gap: 8px;
                }
                #receipt-container .meta-grid span {
                    display: block;
                    text-align: left;
                }
                #receipt-container .section-heading {
                    text-transform: uppercase;
                    font-weight: bold;
                    text-align: center;
                    margin: 6px 0 4px;
                }
            </style>
            <div class="pos-receipt">
                <h3>${t.companyName}</h3>
                <p>${t.branchName}</p>
                <p>${t.address}</p>
                <p>VAT Reg TIN: ${t.vatTin}</p>
                <p>ACCR. NO.: ${t.accrNo}</p>
                <div class="meta-grid" style="margin-top:6px;">
                    <span>Serial #: ${t.serialNo}</span>
                    <span>Permit #: ${t.permitNo}</span>
                    <span>Date: ${H(v)}</span>
                    <span>Time: ${I(v)}</span>
                </div>
                <hr>
                <div style="text-align:left;">
                    <p>Cashier: ${m}</p>
                    <p>Check #: ${i}</p>
                    <p>Guests: ${b}</p>
                    <p>Official Receipt #: ${i}</p>
                </div>
                <hr>
                <div style="text-align:left;">
                    <p class="section-heading">Customer Information</p>
                    <p>Walk-in Customer</p>
                    <p>${t.branchName}</p>
                </div>
                <hr>
                <p class="section-heading">Items on Ticket: ${o.length}</p>
                <table>
                    <tbody>
                        ${rt}
                    </tbody>
                </table>
                <hr>
                <table>
                    <tbody>
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">${T(F)}</td>
                        </tr>
                        <tr>
                            <td>VAT ${t.vatRate?`(${(t.vatRate*100).toFixed(0)}%)`:""}</td>
                            <td class="text-end">${T(nt)}</td>
                        </tr>
                        ${ot}
                        <tr>
                            <td><strong>Amount Due</strong></td>
                            <td class="text-end"><strong>${T(P)}</strong></td>
                        </tr>
                        <tr>
                            <td>Cash</td>
                            <td class="text-end">${T(c)}</td>
                        </tr>
                        <tr>
                            <td>Change</td>
                            <td class="text-end">${T(l)}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <p>Prices inclusive of 10% service charge</p>
                <p>12% VAT Included</p>
                <p>Thank you and please come again.</p>
                <p>For feedback call ${t.hotline}</p>
                <p>Email: ${t.email}</p>
                <p>Visit us at ${t.website}</p>
            </div>
        `;$("#receipt-container").html(it)}$(document).on("click","#printReceiptBtn",function(){window.print()}),$("#orderFinalization .order-type-btn").on("click",function(){$(this).siblings().removeClass("active"),$(this).addClass("active"),$("#order_type_input").val($(this).data("type"))})})});
