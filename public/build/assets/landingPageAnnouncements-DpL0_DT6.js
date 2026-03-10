(function(){const b={gold:"205,164,94",crimson:"192,57,43",forest:"39,174,96",ocean:"41,128,185",royal:"142,68,173",slate:"93,138,168"},h={summer:"255,170,64",christmas:"255,100,80",halloween:"255,140,0",valentines:"255,107,157",newyear:"255,215,0",easter:"201,102,255",ramadan:"218,165,32"};function E(){fetch("/customer-service/announcements-public").then(n=>n.json()).then(({data:n})=>{if(!n||!n.length)return;const l=n.filter(t=>t.type==="announcement"),c=n.filter(t=>t.type==="discount"),e=n.filter(t=>t.type==="promo");l.length&&x(l),e.length&&w(e),c.length&&H(c)}).catch(()=>{})}function x(n){const l=document.getElementById("announcement-ticker"),c=document.getElementById("ticker-icon"),e=document.getElementById("ticker-badge"),t=document.getElementById("ticker-text"),g=document.getElementById("ticker-dots"),f=document.getElementById("ticker-close-btn");if(!l)return;let s=0;function p($){const a=n[$];t.style.opacity="0",setTimeout(()=>{c.className=(a.icon||"fa-solid fa-bullhorn")+" ticker-icon",a.badge_text?(e.textContent=a.badge_text,e.classList.remove("d-none")):e.classList.add("d-none"),t.textContent=a.title+(a.content?" — "+a.content:""),t.style.opacity="1",g.querySelectorAll(".tdot").forEach((d,u)=>{d.classList.toggle("active",u===$)});const o=b[a.theme]??"205,164,94";l.style.borderBottomColor=`rgba(${o}, 0.35)`,c.style.color=`rgb(${o})`,e.style.background=`rgba(${o}, .15)`,e.style.borderColor=`rgba(${o}, .45)`,e.style.color=`rgb(${o})`},200)}n.forEach(($,a)=>{const o=document.createElement("div");o.className="tdot"+(a===0?" active":""),o.addEventListener("click",()=>{s=a,p(a),clearInterval(y),y=setInterval(m,4500)}),g.appendChild(o)}),p(0),l.style.display="block",t.style.transition="opacity .25s";const r=document.querySelector("main.main");function v(){r&&(r.style.marginTop=l.offsetHeight+"px")}v(),window.addEventListener("resize",v);function m(){s=(s+1)%n.length,p(s)}let y=n.length>1?setInterval(m,4500):null;f.addEventListener("click",()=>{clearInterval(y),l.style.display="none",r&&(r.style.marginTop=""),window.removeEventListener("resize",v)})}function w(n){const l=document.getElementById("seasonal-promos"),c=document.getElementById("seasonal-promos-container");!l||!c||(c.innerHTML="",n.forEach(e=>{const t=e.season||"generic",g=e.theme||"gold",f=e.bg_style||"solid",s=t!=="generic"&&h[t]?h[t]:b[g]??"205,164,94",p=t!=="generic"?`seasonal-banner promo-season-${t}`:`seasonal-banner ann-theme-${g} ann-style-${f}`,r=t==="generic"&&f==="glow"?`box-shadow:0 0 40px rgba(${s},.18) inset,0 0 25px rgba(${s},.1);`:"",v=e.badge_text?`<div class="seasonal-badge" style="background:rgba(${s},.12);border-color:rgba(${s},.35);color:rgb(${s})">${i(e.badge_text)}</div>`:"";let m="";if(e.discount_value){const d=e.discount_type==="percentage"?"%":"₱",u=e.discount_type==="percentage"?"OFF":"DISCOUNT",_=e.discount_type==="percentage"?`${e.discount_value}${d}`:`${d}${e.discount_value}`;m=`
                    <div class="seasonal-discount-row">
                        <span class="seasonal-discount-value" style="color:rgb(${s});text-shadow:0 0 28px rgba(${s},.35)">${i(_)}</span>
                        <span class="seasonal-discount-label">${u}</span>
                    </div>`}const y=e.min_spend?`<div class="seasonal-min-spend">Min. spend: ₱${parseFloat(e.min_spend).toFixed(2)}</div>`:"";let $="";if(e.valid_from||e.valid_until){const d=e.valid_from?`From ${e.valid_from}`:"",u=e.valid_until?`Until ${e.valid_until}`:"";$=`<div class="seasonal-validity">${[d,u].filter(Boolean).join(" · ")}</div>`}const a=e.content?`<p class="seasonal-content">${i(e.content)}</p>`:"",o=document.createElement("div");o.setAttribute("data-aos","fade-up"),o.innerHTML=`
                <div class="${p}" style="${r}">
                    <div class="seasonal-banner-overlay"></div>
                    <div class="seasonal-banner-inner">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-9 col-12">
                                ${v}
                                <div class="seasonal-icon-wrap" style="background:rgba(${s},.12);color:rgb(${s})">
                                    <i class="${i(e.icon||"fa-solid fa-wand-magic-sparkles")}"></i>
                                </div>
                                <h2 class="seasonal-title">${i(e.title)}</h2>
                                ${a}
                                ${m}
                                ${y}
                                ${$}
                            </div>
                        </div>
                    </div>
                </div>`,c.appendChild(o)}),l.style.display="block")}function H(n){const l=document.getElementById("promotions"),c=document.getElementById("promotions-grid");!l||!c||(c.innerHTML="",n.forEach(e=>{const t=e.theme||"gold",g=e.bg_style||"solid",f=b[t]??"205,164,94",s=S(t);let p="";e.badge_text&&(p=`<div class="promo-badge">${i(e.badge_text)}</div>`);let r="";if(e.discount_value){const d=e.discount_type==="percentage"?"%":"₱",u=e.discount_type==="percentage"?"OFF":"DISCOUNT",_=e.discount_type==="percentage"?`${e.discount_value}${d}`:`${d}${e.discount_value}`;r=`
                    <div class="promo-discount-value">${i(_)}</div>
                    <div class="promo-discount-label">${u}</div>`}let v="";e.min_spend&&(v=`<div class="promo-min-spend">Min. spend: ₱${parseFloat(e.min_spend).toFixed(2)}</div>`);let m="";if(e.valid_from||e.valid_until){const d=e.valid_from?`From ${e.valid_from}`:"",u=e.valid_until?`Until ${e.valid_until}`:"";m=`<div class="promo-validity">${[d,u].filter(Boolean).join(" · ")}</div>`}let y="";e.content&&(y=`<p class="promo-content">${i(e.content)}</p>`);const $=g==="glow"?`box-shadow: 0 0 35px rgba(${f},.22) inset, 0 0 20px rgba(${f},.14);`:"",a=`--ann-accent:${s};`,o=document.createElement("div");o.className="col-lg-4 col-md-6",o.setAttribute("data-aos","fade-up"),o.innerHTML=`
                <div class="promo-card ann-theme-${t} ann-style-${g}"
                     style="${$}${a}">
                    <div class="promo-card-inner">
                        ${p}
                        <div class="promo-icon-wrap">
                            <i class="${i(e.icon||"fa-solid fa-tag")}"></i>
                        </div>
                        <div class="promo-title">${i(e.title)}</div>
                        ${y}
                        ${r}
                        ${v}
                        ${m}
                    </div>
                </div>`,c.appendChild(o)}),l.style.display="block")}function i(n){return n?String(n).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;"):""}function S(n){return{gold:"#cda45e",crimson:"#c0392b",forest:"#27ae60",ocean:"#2980b9",royal:"#8e44ad",slate:"#5d8aa8"}[n]??"#cda45e"}document.readyState==="loading"?document.addEventListener("DOMContentLoaded",E):E()})();
