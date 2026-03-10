$(document).ready(function(){const l=$('meta[name="csrf-token"]').attr("content");l&&$.ajaxSetup({headers:{"X-CSRF-TOKEN":l}});let d=0;const h=2e3,n=$("#ordersToday"),u="/operations/kds/get-today-orders",x="/operations/kds/check-new-orders",y="/operations/kds/update-status";function f(e,r){if(n.empty(),!e||e.length===0)return n.html(`
                <div class="col-12 text-center my-5 p-5">
                    <h3 class="text-muted">No pending orders found.</h3>
                    <p class="text-muted">New orders will appear here automatically.</p>
                </div>
            `),0;let s=0,o="";return e.forEach(t=>{s=Math.max(s,t.id);let a="";Array.isArray(t.items)&&t.items.forEach(m=>{a+=`<h6 class="text-primary mb-0 ord-item">${m.quantity}x ${m.product_name}</h6>`});const c=r>0&&t.id>r?"bg-light-info":"";o+=`
                <div class="col" data-id="${t.id}">
                    <div class="card shadow product-card-fixed-size d-flex p-2 m-2 ${c}">
                        <div class="card-body p-2 flex-grow-1">
                            <h6 class="card-title mb-1 ord-id">${t.order_id}</h6>
                            <h6 class="text-primary mb-0 ord-date">${t.created_at}</h6>
                            <h6 class="text-primary mb-0 ord-type">${t.order_type}</h6>
                            <h6 class="card-title mb-1 ord-desc">Items:</h6>

                            <div class="ord-items-container">
                                ${a}
                            </div>

                        </div>
                        <div class="footer justify-content-center">
                            <h6 class="text-primary mb-0 ord-price">₱ ${t.total_amount}</h6>
                            <span class="ord-status">${t.status}</span>
                        </div>
                    </div>
                </div>
            `}),n.append(o),r>0&&n.find(".col > .bg-light-info").each(function(){const t=$(this);t.delay(5e3).queue(function(a){t.removeClass("bg-light-info"),a()})}),s}function C(e,r){$.ajax({url:y,type:"POST",dataType:"json",data:{order_id:e,new_status:r},success:function(s){p()},error:function(s,o,t){$("#LoadingScreen").fadeOut(200),Swal.fire("Error","Failed to update order status. Please check the console.","error")}})}function p(){$(n).html(`
             <div class="col-12 text-center my-5 p-5 d-flex flex-column align-items-center">
                <div class="spinner-border" style="width: 3rem; height: 3rem"
                    role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 class="text-muted mt-2">Loading...</h6>
            </div>
            `),$.ajax({url:u,type:"GET",dataType:"json",success:function(e){d=f(e,0)},error:function(e,r,s){}})}p(),setInterval(function(){const e=d;$.ajax({url:x,type:"GET",dataType:"json",success:function(r){r.latest_id>e&&$.ajax({url:u,type:"GET",dataType:"json",success:function(o){d=f(o,e)},error:function(o,t,a){}})},error:function(r,s,o){}})},h),n.on("click",".col",function(){const e=$(this),r=e.data("id"),s=e.find(".ord-id").text().trim();let o=e.find(".ord-status").text().toUpperCase().trim(),t="",a="",i="";if(o.includes("IN QUEUE"))t="In prep",a="You are about to move this order to preparation.",i="Start Prep";else if(o.includes("IN PREPARATION"))t="Ready",a="You are about to declare this order to be ready.",i="Ready";else if(o.includes("READY"))t="Completed",a="You are about to mark this order as completed and remove it from the display.",i="Complete Order";else{if(o.includes("COMPLETED"))return;t="In prep",a="You are about to put this order in preparation.",i="Start Prep"}Swal.fire({title:"Are you sure?",text:a,icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:i}).then(c=>{c.isConfirmed&&($("#LoadingScreen").fadeIn(200),C(r,t),Toast.fire({title:t==="Completed"?"Order Finished!":"Status Updated!",text:`${s} is being updated to ${t}.`,icon:"success",timer:2e3}),$("#LoadingScreen").fadeOut(200))})}),$(document).on("click","#exit-pos-btn",function(e){e.preventDefault(),Swal.fire({title:"Are you sure?",text:"You are about End KDS Session.",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Exit"}).then(r=>{r.isConfirmed&&($("#LoadingScreen").fadeIn(200),window.location.href="/operations")})})});
