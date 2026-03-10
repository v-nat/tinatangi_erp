$(document).ready(function(){function k(){const e=$("#faqAccordion");e.length!==0&&$.ajax({url:"/faqs-public",type:"GET",dataType:"json",success:function(t){e.empty(),t.data&&t.data.length>0?$.each(t.data,function(s,i){const d="collapse-"+i.id,f="heading-"+i.id,o=`
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="${f}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#${d}" aria-expanded="false" aria-controls="${d}">
                                        ${i.question}
                                    </button>
                                </h2>
                                <div id="${d}" class="accordion-collapse collapse" aria-labelledby="${f}"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        ${i.answer}
                                    </div>
                                </div>
                            </div>
                        `;e.append(o)}):e.append(`
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button">
                                    No frequently asked questions are available at this time.
                                </button>
                            </h2>
                        </div>
                    `)},error:function(t){console.error("Failed to load FAQs:",t),e.html(`
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button">
                                Error loading questions. Please try again later.
                            </button>
                        </h2>
                    </div>
                `)}})}k();const m=new Intl.NumberFormat("en-US"),h=new Intl.NumberFormat("en-US",{style:"currency",currency:"PHP",minimumFractionDigits:2,maximumFractionDigits:2}),r="/logo.png",x=e=>e==null||isNaN(e)?"0":m.format(Number(e)),u=e=>e==null||isNaN(e)?h.format(0):h.format(Number(e)),y={weekly:null,monthly:null};function v(e){y[e]&&(y[e].destroy(!0,!0),y[e]=null)}function a(e,t,s=!1){const i=document.getElementById(`${e}-best-seller-wrapper`);i&&(v(e),i.innerHTML=`
            <div class="swiper-slide">
                <div class="py-5 text-center ${s?"text-danger":"text-muted"}">${t}</div>
            </div>
        `)}function g(e){if(!e||e==="N/A")return r;if(e.startsWith("http://")||e.startsWith("https://"))return e;const t=e.replace(/^\/+/,"");return t.startsWith("storage/")?`/${t}`:t.startsWith("app/public/")?`/storage/${t.replace(/^app\/public\//,"")}`:t.startsWith("public/")?`/storage/${t.replace(/^public\//,"")}`:`/storage/app/public/${t}`}function C(e,t){const s=document.getElementById(`${e}-best-seller-wrapper`),i=document.getElementById(`${e}-best-seller-pagination`),d=document.getElementById(`${e}-best-seller-swiper`);if(!s||!d||!i)return;const o=(Array.isArray(t==null?void 0:t.categories)?t.categories:[]).flatMap(l=>(l.items||[]).map(_=>({..._,category_name:l.category_name})));if(o.length===0){a(e,"No sales data available yet. Check back soon!");return}v(e),s.innerHTML=o.map(l=>{const _=g(l.image),b=parseFloat(l.average_unit_price||0),p=b>0?`<small class="d-block mt-2" style="color: rgba(255,255,255,0.75);">Avg ${u(b)}</small>`:"",w=t!=null&&t.label?`<small class="d-block mt-1 text-uppercase" style="letter-spacing: 0.08em; color: rgba(255,255,255,0.6);">${t.label}</small>`:"";return`
                    <div class="swiper-slide">
                        <div class="best-seller-slide h-100 d-flex flex-column justify-content-between align-items-center text-center p-4"
                            style="border-radius: 16px; background-color: rgba(36, 31, 25, 0.95); color: #fff; box-shadow: 0 18px 35px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.08);">
                            <div class="best-seller-image mb-3 rounded-circle overflow-hidden position-relative"
                                style="width: 140px; height: 140px; border: 3px solid rgba(255,255,255,0.35); background: rgba(0,0,0,0.2);">
                                <span class="badge bg-success position-absolute" style="top: -6px; left: -6px; font-size: 0.85rem; border-radius: 999px; padding: 0.35rem 0.6rem;">#${l.global_rank||l.rank||1}</span>
                                <img src="${_}" alt="${l.product_name}"
                                    class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="w-100">
                                <h5 class="fw-semibold mb-1">${l.product_name}</h5>
                                <small class="text-uppercase" style="letter-spacing: 0.1em; color: rgba(255,255,255,0.75);">${l.category_name}</small>
                            </div>
                            <div class="mt-3">
                                <span class="sold-badge rounded-pill px-3 py-2">
                                    ${x(l.total_units)} sold
                                </span>
                                ${p}
                                ${w}
                            </div>
                        </div>
                    </div>
                `}).join(""),y[e]=new Swiper(d,{loop:o.length>1,speed:600,autoplay:{delay:5e3,disableOnInteraction:!1},slidesPerView:1,spaceBetween:20,pagination:{el:i,clickable:!0},breakpoints:{992:{slidesPerView:1,spaceBetween:24}}})}function N(){const e=document.getElementById("weekly-best-seller-wrapper"),t=document.getElementById("monthly-best-seller-wrapper");!e||!t||(a("weekly","Gathering this week's popular picks..."),a("monthly","Checking our monthly best sellers..."),$.ajax({url:"/best-sellers/highlights",type:"GET",data:{limit:1},dataType:"json",success:function(s){function i(o){if(!o||!Array.isArray(o.categories))return o;const l=o.categories.map(n=>{const c=Array.isArray(n.items)?n.items:[];if(!c.length)return null;const F=[...c].sort((E,A)=>Number(A.total_units||0)-Number(E.total_units||0)).slice(0,1).map(E=>({...E}));return!F.length||Number(F[0].total_units||0)<=0?null:{...n,items:F}}).filter(Boolean),b=[...l.flatMap(n=>(n.items||[]).map(c=>({...c,category_id:n.category_id})))].sort((n,c)=>Number(c.total_units||0)-Number(n.total_units||0)),p=new Map;b.forEach((n,c)=>{p.set(`${n.category_id}-${n.product_id}`,c+1)});const w=l.map(n=>({...n,items:(n.items||[]).map(c=>({...c,global_rank:p.get(`${n.category_id}-${c.product_id}`)||1}))}));return{...o,categories:w}}const d=i(s.weekly||{}),f=i(s.monthly||{});$("#public-weekly-range").text(d.label||"No data yet"),$("#public-monthly-range").text(f.label||"No data yet"),C("weekly",d),C("monthly",f)},error:function(){a("weekly","Unable to load weekly best sellers right now.",!0),a("monthly","Unable to load monthly best sellers right now.",!0)}}))}N();var T=raterJs({starSize:28,step:.5,element:document.querySelector("#food-rater"),rateCallback:function(t,s){this.setRating(t),s()}}),I=raterJs({starSize:28,step:.5,element:document.querySelector("#staff-rater"),rateCallback:function(t,s){this.setRating(t),s()}}),j=raterJs({starSize:28,step:.5,element:document.querySelector("#environment-rater"),rateCallback:function(t,s){this.setRating(t),s()}});$("#submitFeedback").on("click",function(e){e.preventDefault();let t=!0;const s=$("#serviceFeedbackForm"),i=I.getRating(),d=T.getRating(),f=j.getRating(),o=$("#photo");if(s.find(".is-invalid").removeClass("is-invalid"),$("#image_error").text(""),o.removeClass("is-invalid"),s.find("input[required], select[required], textarea[required]").each(function(){$(this).val()||($(this).addClass("is-invalid"),Toast.fire({icon:"warning",text:"Please fill all fields before submitting."}),t=!1)}),(i===null||d==null||f==null)&&(Toast.fire({icon:"warning",text:"Please fill and rate all fields before submitting."}),t=!1),o[0].files.length>0){const p=o[0].files[0],w=["image/jpeg","image/png","image/gif","image/jpg"],n=10*1024*1024;if(w.includes(p.type)||(t=!1,o.addClass("is-invalid"),Toast.fire({icon:"error",text:"Invalid file type. Please use jpg, jpeg, or png."}),$("#image_error").text("Invalid file type. Please use jpg, jpeg, or png.")),p.size>n){t=!1,o.addClass("is-invalid");const c=$("#image_error").text(),F="File is too large. Maximum size is 10MB.";Toast.fire({icon:"error",text:"File is too large. Maximum size is 10MB."}),$("#image_error").text(c?`${c} ${F}`:F)}}if(!t)return;const l=(i+d+f)/3,_=$("#serviceFeedbackForm");let b=new FormData(_[0]);b.append("food_rating",d),b.append("staff_rating",i),b.append("environment_rating",f),b.append("overall_rating",l.toFixed(2)),$.ajax({url:"/customer-service/submit-feedback",method:"POST",data:b,headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},processData:!1,contentType:!1,beforeSend:function(){$("#LoadingScreen").fadeIn(200)},success:function(p){$("#LoadingScreen").fadeOut(200),Toast.fire({icon:"success",text:p.message||"Thank you for your feedback!"}),_[0].reset(),T.clear(),I.clear(),j.clear()},error:function(p){$("#LoadingScreen").fadeOut(200);const w=p.responseJSON.errors;let n="An error occurred. Please try again.";w&&(n=Object.values(w).flat().join(`
`)),Toast.fire({icon:"error",text:n})}})});const M=$("#message"),S=$("#char-count"),q=M.attr("maxlength");S.text(q+" characters remaining"),M.on("input",function(){let e=$(this).val().length,t=q-e;S.text(t+" characters remaining"),t<20?S.addClass("text-warning"):S.removeClass("text-warning"),t<1?S.addClass("text-danger"):S.removeClass("text-danger")})});$(document).ready(function(){function k(r){return r.toString().replace(/"/g,"&quot;").replace(/'/g,"&#39;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}const m=$("#testimonials .init-swiper"),h=m.find(".swiper-wrapper");$.ajax({url:"/testimonials",method:"GET",success:function(r){if(r&&r.length>0){h.empty(),r.forEach(u=>{const y=u.photo?`/storage/app/public/${u.photo}`:"assets/img/default-avatar.png",v=u.message||"No comments provided.",a=100;let g="";if(v.length>a){const N=v.substring(0,a)+"...",T=k(v);g=`
                            <span>${N}</span>
                            <i class="bi bi-quote quote-icon-right"></i>
                            <a href="#" class="see-more-btn-testimonial ms-1"
                               data-bs-toggle="modal"
                               data-bs-target="#messageModal"
                               data-full-message="${T}"
                               style="font-size: 14px; color: #cda45e; font-weight: 500;">
                               See More
                            </a>
                        `}else g=`
                            <span>${v}</span>
                            <i class="bi bi-quote quote-icon-right"></i>
                        `;const C=`
                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <p>
                                    <i class="bi bi-quote quote-icon-left"></i>
                                    ${g}
                                </p>
                                <img src="${y}" class="testimonial-img" alt="No photo provided.">
                                <h3>${u.name}</h3>
                                <h4>Valued Customer</h4>
                            </div>
                        </div>
                    `;h.append(C)}),m[0].swiper&&m[0].swiper.destroy(!0,!0);const x=JSON.parse(m.find(".swiper-config").text());new Swiper(m[0],x)}},error:function(r){console.error("Failed to load testimonials:",r)}}),$(document).on("click",".see-more-btn-testimonial",function(r){r.preventDefault();const x=$(this).data("full-message");$("#modalMessageBody").text(x)})});$(document).ready(function(){const k=$(".isotope-container"),m=$(".menu-filters"),h=$("#menu-loading");$.ajax({url:"/menu/products",type:"GET",dataType:"json",success:function(r){if(r.data&&Array.isArray(r.data)){let x=[],u={};r.data.forEach(a=>{const g=a.image;let C=g&&g!=="N/A"?"/storage/app/public/"+g:DEFAULT_PRODUCT_IMAGE;const N=parseFloat(a.base_price||0).toFixed(2);x.push(`
                        <div class="col-lg-6 menu-item isotope-item ${a.filter_class}">
                            <img src="${C}" class="menu-img" alt="${a.name}">
                            <div class="menu-content">
                                <a href="#">${a.name}</a><span>₱${N}</span>
                            </div>
                            <div class="menu-ingredients">
                                ${a.description}
                            </div>
                        </div>
                    `),u[a.category_name]||(u[a.category_name]=a.filter_class)});let y=[];for(const a in u){const g=u[a];y.push(`
                        <li data-filter=".${g}">${a}</li>
                    `)}h.remove(),m.append(y.join("")),k.html(x.join(""));let v=new Isotope(k[0],{itemSelector:".isotope-item",layoutMode:"masonry"});m.on("click","li",function(){m.find(".filter-active").removeClass("filter-active"),$(this).addClass("filter-active"),v.arrange({filter:$(this).data("filter")})})}else h.html('<p class="text-danger">Could not load menu items.</p>')},error:function(r){console.error("Failed to load menu:",r.responseText),h.html('<p class="text-danger">Error: Could not load the menu. Please try again later.</p>')}})});
