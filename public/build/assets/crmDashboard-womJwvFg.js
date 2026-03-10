$(document).ready(function(){const c=new Intl.NumberFormat("en-US"),w={chart:{type:"area",height:340,toolbar:{show:!1}},dataLabels:{enabled:!1},stroke:{curve:"smooth",width:2},series:[{name:"Bookings",data:[]}],xaxis:{categories:[],labels:{rotate:-45}},yaxis:{min:0,forceNiceScale:!0},tooltip:{shared:!0,x:{format:"MMM d, yyyy"}},noData:{text:"Loading booking trend..."},colors:["#7367F0"]},D={chart:{type:"donut",height:320},labels:[],series:[],dataLabels:{enabled:!0,formatter:t=>t&&`${t.toFixed(0)}%`},legend:{position:"bottom",fontSize:"14px"},noData:{text:"Loading status data..."},colors:["#EA5455","#28C76F","#FF9F43","#7367F0","#1E90FF"]},S={chart:{type:"radar",height:240},series:[{name:"Average Rating",data:[]}],xaxis:{categories:["Food","Staff","Environment"]},yaxis:{min:0,max:5,tickAmount:5,labels:{formatter:t=>t.toFixed(0)}},markers:{size:4},noData:{text:"Loading ratings..."},colors:["#28C76F"]},A={chart:{type:"bar",height:240,toolbar:{show:!1}},plotOptions:{bar:{distributed:!0,horizontal:!1,columnWidth:"55%",borderRadius:4}},dataLabels:{enabled:!1},series:[{name:"Count",data:[]}],xaxis:{categories:[]},legend:{show:!1},noData:{text:"Loading distribution..."},colors:["#EA5455","#FF9F43","#7367F0","#28C76F","#00CFE8"]},Y={chart:{type:"line",height:310,toolbar:{show:!1}},stroke:{width:[2,2],dashArray:[0,6],curve:"smooth"},markers:{size:[3,5]},series:[{name:"Actual",data:[]},{name:"Forecast",data:[]}],xaxis:{categories:[],labels:{rotate:-45,style:{fontSize:"11px"}}},yaxis:{min:0,forceNiceScale:!0,title:{text:"Bookings"}},legend:{position:"top",horizontalAlign:"right"},colors:["#7367F0","#FF9F43"],annotations:{xaxis:[]},tooltip:{shared:!0,intersect:!1},noData:{text:"Loading forecast..."}},x=new ApexCharts(document.querySelector("#chart-bookings-trend"),w);x.render();const b=new ApexCharts(document.querySelector("#chart-booking-status"),D);b.render();const y=new ApexCharts(document.querySelector("#chart-category-ratings"),S);y.render();const f=new ApexCharts(document.querySelector("#chart-ratings-distribution"),A);f.render();const h=new ApexCharts(document.querySelector("#chart-booking-forecast"),Y);h.render();function C(t,a){if(!t)return"—";const n=a?`${t} ${a}`:t,o=dayjs(n);return o.isValid()?o.format(a?"MMM D, YYYY • h:mm A":"MMM D, YYYY"):dayjs(t).format("MMM D, YYYY")}function k(t){return $("<div/>").text(t??"").html()}function N(t,a=100){return t?t.length>a?`${t.substring(0,a)}…`:t:""}function F(t,a,n){if(a.empty(),!t||t.length===0){a.append(`<li class="list-group-item text-muted text-center">${n}</li>`);return}t.forEach(o=>{var u;const d=k(o.name||"Anonymous"),s=o.overall_rating!=null?Number(o.overall_rating).toFixed(1):"0.0",r=o.created_at?dayjs(o.created_at).format("MMM D, YYYY"):"—",i=N(((u=o.message)==null?void 0:u.trim())??"",120),l=i?k(i):'<span class="text-muted fst-italic">No written feedback.</span>';a.append(`
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="pe-3">
                            <h6 class="mb-1">${d}</h6>
                            <p class="mb-1 small text-muted">${l}</p>
                            <small class="text-muted">${r}</small>
                        </div>
                        <div class="ms-2 text-nowrap">
                            <span class="badge bg-warning text-dark">
                                <i class="fa-solid fa-star me-1"></i>${s}
                            </span>
                        </div>
                    </div>
                </li>
            `)})}function j(t){const a=$("#forecast-insights-list");if(a.empty(),!t||t.length===0){$("#forecast-peak-day").text("—"),$("#forecast-total-week").text("—");return}const n=Math.max(...t.map(s=>s.predicted)),o=t.reduce((s,r)=>s+r.predicted,0),d=t.find(s=>s.predicted===n);$("#forecast-peak-day").text(d?dayjs(d.date).format("ddd, MMM D"):"—"),$("#forecast-total-week").text(Math.round(o)),t.forEach(s=>{const r=n>0?s.predicted/n*100:0,i=s.predicted===n;a.append(`
                <div class="d-flex align-items-center mb-2">
                    <div class="me-2 text-muted" style="width:80px;font-size:12px;">
                        ${dayjs(s.date).format("ddd M/D")}
                    </div>
                    <div class="flex-grow-1">
                        <div class="progress" style="height:14px;border-radius:7px;">
                            <div class="progress-bar ${i?"bg-warning":"bg-primary"}"
                                 style="width:${r.toFixed(1)}%"></div>
                        </div>
                    </div>
                    <div class="ms-2 fw-bold text-end" style="width:36px;font-size:13px;">
                        ${s.predicted}
                    </div>
                </div>
            `)})}function L(){$.ajax({url:"/customer-service/dashboard-analytics",type:"GET",dataType:"json",success:function(t){const a=t.kpis||{},n=a.averageRating!=null?Number(a.averageRating).toFixed(2):"0.00";$("#kpi-average-rating").text(`${n} ★`),$("#kpi-upcoming-bookings").text(c.format(a.upcomingBookings||0)),$("#kpi-pending-bookings").text(c.format(a.pendingBookings||0)),$("#kpi-feedback-pending").text(c.format(a.pendingFeedback||0)),$("#kpi-active-announcements").text(c.format(a.activeAnnouncements||0));const o=t.bookingsTrend||[],d=o.map(e=>{const g=dayjs(e.date);return g.isValid()?g.format("MMM D"):e.date||""});x.updateSeries([{data:o.map(e=>e.count||0)}]),x.updateOptions({xaxis:{categories:d}});const s=t.statusBreakdown||[];b.updateOptions({labels:s.map(e=>e.label)}),b.updateSeries(s.map(e=>e.count||0));const r=$("#table-upcoming-bookings tbody");r.empty();const i=t.upcomingBookings||[];i.length===0?r.append('<tr><td colspan="5" class="text-center text-muted">No upcoming bookings scheduled.</td></tr>'):i.forEach(e=>{const g=C(e.date,e.time),R=e.status_badge||e.status_label||"—";r.append(`
                            <tr>
                                <td>${e.name||"—"}</td>
                                <td>${c.format(e.people||0)}</td>
                                <td>${g}</td>
                                <td>${e.table||"Not assigned"}</td>
                                <td>${R}</td>
                            </tr>
                        `)});const l=t.categoryRatings||{};y.updateSeries([{data:[parseFloat(Number(l.food||0).toFixed(2)),parseFloat(Number(l.staff||0).toFixed(2)),parseFloat(Number(l.environment||0).toFixed(2))]}]);const u=t.ratingsDistribution||[];f.updateSeries([{data:u.map(e=>e.count||0)}]),f.updateOptions({xaxis:{categories:u.map(e=>e.rating_label)}}),F(t.topRatedFeedback||[],$("#list-top-feedback"),"No top rated feedback yet."),F(t.recentFeedback||[],$("#list-recent-feedback"),"No feedback submitted recently.");const p=t.recentActual||[],m=t.forecast||[],v=[...p.map(e=>dayjs(e.date).format("MMM D")),...m.map(e=>dayjs(e.date).format("MMM D"))],z=[...p.map(e=>e.count),...m.map(()=>null)],O=[...p.map(()=>null),...m.map(e=>e.predicted)],M=p.length>0?v[p.length-1]:null;h.updateOptions({xaxis:{categories:v},annotations:{xaxis:M?[{x:M,borderColor:"#ea5455",borderWidth:2,label:{style:{color:"#fff",background:"#ea5455",fontSize:"11px"},text:"Today →",position:"top",orientation:"horizontal"}}]:[]}}),h.updateSeries([{name:"Actual",data:z},{name:"Forecast",data:O}]),j(m)},error:function(t){console.error("Failed to load dashboard analytics:",t)}})}L()});
