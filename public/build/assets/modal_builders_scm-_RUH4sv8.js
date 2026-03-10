function D(t){const a=new Set;let n="",r=0;t.purchase_orders&&t.purchase_orders.length>0&&t.purchase_orders.forEach(m=>{a.add(m.created_by_id||"N/A");const d=m.details||[];d.length>0&&d.forEach(e=>{r++;const o=!!e.is_returned,p=!!e.is_partial_return;let c="",g="";o?(c=' class="table-danger"',g='<span class="badge bg-danger ms-2">Returned</span>'):p&&(c=' class="table-warning"',g='<span class="badge bg-warning text-dark ms-2">Partial Return</span>');const b=p?`${e.delivered_qty}/${e.ordered_qty} ${e.item_unit||""}`:`${e.quantity||0} ${e.item_unit||"N/A"}`;n+=`
                        <tr${c}>
                            <td>${r}</td>
                            <td>${e.item_name||"N/A"} ${g}</td>
                            <td>${e.item_unit_name||"N/A"}</td>
                            <td class="text-end">₱${parseFloat(e.unit_price||0).toFixed(2)}</td>
                            <td class="text-end">${b}</td>
                            <td class="text-end">₱${parseFloat(e.total_amount||0).toFixed(2)}</td>
                        </tr>
                        `})});const h=Array.from(a).join(", ");n===""&&(n='<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>');let l="";t.overall_photo_path&&(l=`
        <hr>
        <div class="row mt-3 px-3">
            <div class="col-12">
                <h6 class="mb-2">Delivery Proof</h6>
                <a href="${t.overall_photo_path}" target="_blank">
                    <img src="${t.overall_photo_path}" class="img-fluid img-thumbnail" alt="Delivery Proof" style="max-height: 350px; width: auto;">
                </a>
            </div>
        </div>
        `);const i=`
    <div class="row mb-4 p-3">
        <div class="col-md-6">
            <p class="mb-0">Requested by: ${h||"N/A"}</p>
            <p class="mb-0">Supplier: ${t.supplier_name}</p>
            <p class="mb-0">Delivery #: ${t.delivery_no||"N/A"}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <p id="view_invoice_number" class="mb-0">Invoice #: ${t.id||"N/A"}</p>
            <p class="mb-0">Date Approved: ${t.date_approved||"N/A"}</p>
            <p class="mb-0">Approved By: ${t.approved_by_id||"N/A"}</p>
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
                    ${n}
                    <tr>
                        <td colspan="5" class="text-end"><strong>Total Amount:</strong></td>
                        <td colspan="5" class="text-end"><strong>₱${parseFloat(t.total_amount||0).toFixed(2)}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    ${l}

    <style>
    .table-sm td,
    .table-sm th {
        padding: 0.4rem 0.6rem;
        font-size: 0.875rem;
    }
    </style>
    `;$("#LoadingScreen").fadeOut(200),$("#viewInvoice .modal-body").html(i),$("#viewInvoice").modal("show")}function R(t){let a="",n=0;t.purchase_orders&&t.purchase_orders.length>0&&t.purchase_orders.forEach(h=>{const l=h.details||[];l.length>0&&l.forEach(i=>{n++;const m=!!i.is_returned,d=!!i.is_partial_return;let e="",o="";m?(e=' class="table-danger"',o='<span class="badge bg-danger ms-2">Returned</span>'):d&&(e=' class="table-warning"',o='<span class="badge bg-warning text-dark ms-2">Partial Return</span>');const p=d?`${i.delivered_qty}/${i.ordered_qty} ${i.item_unit||""}`:`${i.quantity||0} ${i.item_unit||"N/A"}`;a+=`
                        <tr${e}>
                            <td>${n}</td>
                            <td>${i.item_name||"N/A"} ${o}</td>
                            <td>${i.item_unit_name||"N/A"}</td>
                            <td class="text-end">₱${parseFloat(i.unit_price||0).toFixed(2)}</td>
                            <td class="text-end">${p}</td>
                            <td class="text-end">₱${parseFloat(i.total_amount||0).toFixed(2)}</td>
                        </tr>
                    `})}),a===""&&(a='<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>');const r=`
        <div class="row mb-4 p-3">
            ${t.status}
            <!-- Purchase Request Header -->
            <div class="col-md-6">
                <h6 class="mb-0">Requested By: <strong>${t.requested_by_id||"N/A"}</strong></h6>
                <p class="mb-0">Department: ${t.department||"N/A"}</p>
                <p class="mb-0">Supplier: <strong class="text-success">${t.supplier_name}</strong></p> <!-- SUPPLIER MOVED HERE -->
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="mb-0">Purchase Request ID: <strong>${t.id||"N/A"}</strong></h6>
                <p class="mb-0">Requested Date: ${t.requested_date||"N/A"}</p>
                <p class="mb-0">Total PR Amount: <strong class="text-primary">₱${parseFloat(t.total_amount||0).toFixed(2)}</strong></p>
            </div>
            <div class="col-md-12 mt-3">
                <p class="mb-0">Remarks: <em>${t.remarks||"None"}</em></p>
            </div>
        </div>

        <hr class="mt-0">

        <div class="px-3">
            <h5 class="mb-3 text-primary">All Associated Line Items</h5>
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
                        ${a}
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
    `;$("#LoadingScreen").fadeOut(200),$("#viewPO .modal-body").html(r),$("#viewPO").modal("show")}function T(){const t=$("#viewInvoice"),a=document.title;let n="N/A";const r=t.find("#view_invoice_number");r.length&&(n=r.text().replace("Invoice #: ","").trim());const h=t.find('p:contains("Date Approved:")').text().replace("Date Approved: ","").trim()||new Date().toLocaleDateString(),l=t.find('p:contains("Delivery #:")').text().replace("Delivery #: ","").trim()||"N/A",i=t.find('p:contains("Approved By:")').text().replace("Approved By: ","").trim()||"N/A",m=t.find('p:contains("Requested by:")').text().replace("Requested by: ","").trim()||"N/A",d=t.find('td:contains("Total Amount:")').next().text().trim()||"₱0.00",e=[];t.find("table tbody tr").each(function(){const s=$(this);s.find('td:contains("Total Amount:")').length>0||e.push({name:s.find("td:nth-child(2)").text().trim(),unit:s.find("td:nth-child(3)").text().trim(),unitPrice:s.find("td:nth-child(4)").text().trim(),quantity:s.find("td:nth-child(5)").text().trim().split(" ")[0],total:s.find("td:nth-child(6)").text().trim()})});const o=t.find('img[alt="Delivery Proof"]'),p=o.length?o.attr("src"):null,c=new Date,b=`${c.getFullYear()+"-"+(c.getMonth()+1).toString().padStart(2,"0")+"-"+c.getDate().toString().padStart(2,"0")}-invoice-${n}-Tinatangi-Cafe`;document.title=b;let f="";e.forEach(s=>{f+=`
            <tr>
                <td class="text-center">${s.quantity}</td>
                <td>${s.unit}</td>
                <td>${s.name}</td>
                <td class="text-end">${s.unitPrice}</td>
                <td class="text-end">${s.total}</td>
            </tr>
        `});const N=`
        <div class="print-invoice-page">
            <header class="print-header">
                <div class="header-left">
                    <h1 class="company-name">Tinatangi Cafe</h1>
                    <p>Brgy 13 Jose Abad Santos Ave, Dasmariñas, 4114 Cavite</p>
                </div>
                <div class="header-right">
                    <h1 class="invoice-title">SALES INVOICE</h1>
                    <div class="invoice-num-box">
                        <span class="invoice-num-label">Nº</span>
                        <span class="invoice-num">${n}</span>
                    </div>
                </div>
            </header>

            <section class="print-customer-details">
                <div class="sold-to">
                    <p><strong>SOLD TO:</strong> Tinatangi Cafe</p>
                    <p><strong>ADDRESS:</strong> Brgy 13 Jose Abad Santos Ave, Dasmariñas, 4114 Cavite</p>
                    <p><strong>BUSINESS STYLE:</strong> Restaurant Coffee Shop</p>
                </div>
                <div class="invoice-meta">
                    <p><strong>DATE:</strong> ${h}</p>
                    <p><strong>P.O. #:</strong> ${l}</p>
                </div>
            </section>

            <section class="print-item-table">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 8%;">QTY.</th>
                            <th style="width: 12%;">UNIT</th>
                            <th style="width: 45%;">PARTICULARS</th>
                            <th style="width: 15%;" class="text-end">UNIT PRICE</th>
                            <th style="width: 20%;" class="text-end">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${f}
                    </tbody>
                </table>
            </section>

            <footer class="print-footer">
                <div class="summary-left">
                    <div class="summary-box">
                        <div class="summary-row total">
                            <span><strong>TOTAL AMOUNT</strong></span>
                            <span><strong>${d}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="signatures-right">
                    <div class="sig-box">
                        <strong>Prepared by:</strong>
                        <p class="sig-line"></p>
                        <p class="sig-name">${m}</p>
                    </div>
                    <div class="sig-box">
                        <strong>Approved by:</strong>
                        <p class="sig-line"></p>
                        <p class="sig-name">${i}</p>
                    </div>
                    <div class="received-box">
                        <p>Received goods in good order and condition</p>
                        <p class="sig-line"></p>
                        <p class="sig-name">Please Print Name & Sign</p>
                    </div>
                </div>
            </footer>
        </div>
    `;let x="";p&&(x=`
            <div class="delivery-proof-page">
                <h2 class="proof-title">Delivery Proof</h2>
                <img src="${p}" alt="Delivery Proof">
            </div>
        `);const P=N+x;$("body").append(`<div id="temp-print-content">${P}</div>`);const y=$('<style type="text/css" id="print-temp-style">').text(`
        @media print {
            body > *:not(#temp-print-content) {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 0.5in;
            }

            body {
                margin: 0 !important;
                padding: 0 !important;
                color: #000 !important;
                font-family: 'Arial', sans-serif;
                font-size: 8.5pt;
            }

            #temp-print-content {
                display: block !important;
                visibility: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .text-end { text-align: right; }
            .text-center { text-align: center; }
            p { margin: 1px 0; }

            .print-invoice-page {
                width: 100%;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .print-header {
                display: flex;
                justify-content: space-between;
                border-bottom: 2px solid #000;
                padding-bottom: 5px;
            }
            .header-left .company-name {
                font-size: 12pt;
                font-weight: bold;
                margin: 0;
            }
            .header-right {
                text-align: right;
            }
            .header-right .invoice-title {
                font-size: 14pt;
                font-weight: bold;
                margin: 0;
                color: #333;
            }
            .header-right .invoice-num-box {
                border: 1px solid #000;
                padding: 2px 5px;
                display: inline-block;
                margin-top: 5px;
            }
            .invoice-num-label {
                font-weight: bold;
                margin-right: 10px;
            }
            .invoice-num {
                font-weight: bold;
                font-size: 11pt;
            }

            .print-customer-details {
                display: flex;
                margin-top: 10px;
                padding-bottom: 10px;
                border-bottom: 1px solid #000;
            }
            .sold-to { flex: 3; }
            .invoice-meta { flex: 2; }

            .print-item-table {
                margin-top: 5px;
                flex-grow: 1; /* This makes the table fill the space */
            }
            .print-item-table table {
                width: 100%;
                border-collapse: collapse;
            }
            .print-item-table th,
            .print-item-table td {
                border: 1px solid #000;
                padding: 3px 5px;
                vertical-align: top;
            }
            .print-item-table th {
                background-color: #eee;
                text-align: center;
                font-size: 8pt;
            }
            .print-item-table tbody tr:last-child td {
                 border-bottom: 1px solid #000;
            }

            .print-footer {
                display: flex;
                justify-content: space-between;
                margin-top: 10px;
                padding-top: 10px;
                border-top: 2px solid #000;
                width: 100%;
            }

            .summary-left {
                flex: 1.2;
                display: flex;
                align-items: center;
            }
            .summary-box {
                border: 1px solid #000;
                width: 90%;
            }
            .summary-row.total {
                display: flex;
                justify-content: space-between;
                padding: 8px 10px;
                font-size: 10pt;
                font-weight: bold;
                background-color: #eee;
            }

            .signatures-right {
                flex: 1.5;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .sig-box {
                margin-bottom: 10px;
            }
            .sig-line {
                border-bottom: 1px solid #000;
                margin-top: 15px;
                margin-bottom: 2px;
            }
            .sig-name {
                font-size: 8pt;
                text-align: center;
            }
            .received-box {
                font-weight: bold;
            }

            .delivery-proof-page {
                page-break-before: always;
                padding-top: 0.5in;
                text-align: center;
            }
            .delivery-proof-page .proof-title {
                font-size: 14pt;
                font-weight: bold;
                margin-bottom: 15px;
            }
            .delivery-proof-page img {
                max-width: 90%;
                border: 1px solid #999;
                padding: 5px;
            }
        }
    `);$("head").append(y);const w=()=>{y.remove(),$("#temp-print-content").remove(),document.title=a,window.removeEventListener("focus",w),u&&u.removeListener(A)};let _=!1;const v=()=>{_||(_=!0,w())};window.addEventListener("focus",v);const A=s=>{s.matches||v()},u=window.matchMedia("print");u.addListener(A),window.print(),setTimeout(v,1500)}export{D as a,R as b,T as p};
