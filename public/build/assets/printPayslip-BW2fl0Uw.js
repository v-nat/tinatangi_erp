function x(t){const s=`
        <div class="row mb-4 px-48">
            ${t.remarks}
            <div class="col-md-6">
                <h6 class="mb-1">Employee: <strong>${t.name}</strong></h6>
                <p class="mb-0">Department: ${t.department}</p>
                <p class="mb-0">Position: ${t.position}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="mb-1">Pay Period: <strong>${t.start_date} - ${t.end_date}</strong></h6>
                <h6 class="mb-1">Working Days: ${t.working_days}</h6>
            </div>
            <div class="col-md-6 mt-5">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <th>Earnings</th>
                            <th>Amount</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-bold-500">Days Present</td>
                                <td>${t.days_present} days</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Total Hours Pay</td>
                                <td>${t.total_hours} hours</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Regular Pay</td>
                                <td>₱ ${parseFloat(t.reg_pay).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Overtime Pay</td>
                                <td>₱ ${parseFloat(t.overtime_pay).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Leave Pay</td>
                                <td>₱ ${parseFloat(t.leave_pay).toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6 mt-5 text-md-end">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <th>Deductions</th>
                            <th>Amount</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-bold-500">Days Absent Deduction</td>
                                <td>₱ ${parseFloat(t.absent_deduction).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Tardiness Deduction</td>
                                <td>₱ ${parseFloat(t.tardiness_deduction).toLocaleString()}</td>
                            </tr>
                            <tr class="table-light">
                                <td colspan="2" class="small text-muted fw-semibold">Mandatory Contributions</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">SSS <span class="badge bg-primary" style="font-size:0.65rem">EE</span></td>
                                <td>₱ ${parseFloat(t.sss??0).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">SSS <span class="badge bg-secondary" style="font-size:0.65rem">ER</span></td>
                                <td class="text-muted">₱ ${parseFloat(t.sss_employer??0).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">PhilHealth <span class="badge bg-primary" style="font-size:0.65rem">EE</span></td>
                                <td>₱ ${parseFloat(t.philhealth??0).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">PhilHealth <span class="badge bg-secondary" style="font-size:0.65rem">ER</span></td>
                                <td class="text-muted">₱ ${parseFloat(t.philhealth_employer??0).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Pag-IBIG <span class="badge bg-primary" style="font-size:0.65rem">EE</span></td>
                                <td>₱ ${parseFloat(t.pagibig??0).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Pag-IBIG <span class="badge bg-secondary" style="font-size:0.65rem">ER</span></td>
                                <td class="text-muted">₱ ${parseFloat(t.pagibig_employer??0).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Tax</td>
                                <td>₱ ${parseFloat(t.tax_deduction??0).toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
            </div>
            <div class="col-md-6 text-md-start">
                <h6 class="mb-1">Gross Pay: <strong>₱ ${parseFloat(t.gross_pay).toLocaleString()}</strong></h6>
                <h6 class="mb-1">Gross Deduction: <strong>₱ ${parseFloat(t.gross_deduction).toLocaleString()}</strong></h6>
                <h6 class="mb-1">Salary Before Tax: <strong>₱ ${parseFloat(t.salary_before_tax).toLocaleString()}</strong></h6>
                <br>
                <h6 class="mb-1">Net Pay: <strong>₱ ${parseFloat(t.net_pay).toLocaleString()}</strong></h6>
            </div>
        </div>
        <style>
        .table-sm td,
        .table-sm th {
            padding: 0.4rem 0.6rem; /* tighter vertical and horizontal spacing */
            font-size: 0.875rem;    /* slightly smaller text */
        }
        </style>
    `;$("#LoadingScreen").fadeOut(200),$("#viewPayroll .modal-body").html(s),$("#viewPayroll").modal("show")}function f(t){const s=[{label:"Days Present",days:t.days_present},{label:"Regular Hours Worked",hours:t.total_hours},{label:"Regular Pay",value:t.reg_pay},{label:"Overtime Pay",value:t.overtime_pay},{label:"Leave Pay",value:t.leave_pay}],r=[{label:"Days Absent Deduction",value:t.absent_deduction},{label:"Tardiness Deduction",value:t.tardiness_deduction},{label:"SSS",value:t.sss},{label:"PhilHealth",value:t.philhealth},{label:"Pag-IBIG",value:t.pagibig},{label:"Tax",value:t.tax_deduction}];let i="";const c=Math.max(s.length,r.length),m=new Date().toLocaleString("en-US",{year:"numeric",month:"long",day:"numeric",hour:"2-digit",minute:"2-digit"});for(let o=0;o<c;o++){const e=s[o],a=r[o];i+=`
            <tr>
                ${e?e.value?`<td class="text-bold-500">${e.label}</td><td>₱ ${parseFloat(e.value).toLocaleString()}</td>`:e.days?`<td class="text-bold-500">${e.label}</td><td>${e.days} days</td>`:e.hours?`<td class="text-bold-500">${e.label}</td><td>${e.hours} hours</td>`:"<td></td><td></td>":"<td></td><td></td>"}
                ${a?a.employer?`<td style="color:#666;font-style:italic">${a.label}</td><td style="color:#666;font-style:italic">₱ ${parseFloat(a.value??0).toLocaleString()}</td>`:`<td class="text-bold-500">${a.label}</td><td>₱ ${parseFloat(a.value??0).toLocaleString()}</td>`:"<td></td><td></td>"}
            </tr>
        `}const b=`
        <div class="payslip-print-container">
            <div class="row d-flex justify-content-center align-items-center mb-3">
                <div class="col-6 col-md-6">
                    <img src="/tinatangilogo2 - Copy.png" style="width:auto; height: 40px !important;" alt="Tinatangi Logo">
                </div>
                <div class="col-6 col-md-6 text-end">
                    <p class="mb-0">${m}</p>
                </div>
            </div>

            <div class="d-flex mb-1">
                <div>
                    <h6 style="color:#A9A9A9; margin-bottom:0">Tinatangi Cafe ERP Management System</h6>
                    <p style="color:#A9A9A9"><strong>System Generated Employee Payslip</strong></p>
                </div>
            </div>
            <div class="row px-48">
                <div class="row d-flex m-0 p-0">
                    <div class="col-md-6">
                        <p class="mb-0">Employee: ${t.name}</p>
                        <p class="mb-0">Department: ${t.department}</p>
                        <p class="mb-0">Position: ${t.position}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                    <p class="mb-0">Starting Date: ${t.start_date}</p>
                    <p class="mb-0">End Date: ${t.end_date}</p>
                    <p class="mb-0">Working Days: ${t.working_days}</p>
                    </div>
                </div>
                <!-- Combined Earnings and Deductions Table -->
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Earnings</th>
                                    <th>Amount</th>
                                    <th>Deductions</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${i}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Gross Pay:</td>
                                    <td>₱ ${parseFloat(t.gross_pay).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Gross Deduction:</td>
                                    <td>₱ ${parseFloat(t.gross_deduction).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Salary Before Tax:</td>
                                    <td>₱ ${parseFloat(t.salary_before_tax).toLocaleString()}</td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Net Pay:</td>
                                    <td><strong>₱ ${parseFloat(t.net_pay).toLocaleString()}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `,d=$('<div id="temp-print-content"></div>').html(b);$("body").append(d);const p=$('<style type="text/css" id="print-temp-style">').text(`
        @media print {
            /* Hide everything on the page by default */
            body > *:not(#temp-print-content) {
                display: none !important;
            }

            /* Global Body Reset for Print */
            body {
                margin: 0 !important;
                padding: 0 !important;
                font-size: 10pt;
                color: #000 !important;
                background-color: #fff !important;
            }

            /* --- CRITICAL FULL-PAGE EXPANSION --- */
            #temp-print-content {
                display: block !important;
                visibility: visible !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100% !important;
                max-width: 100vw !important;
                margin: 0 !important;
                padding: 1.5rem !important; /* Add standard printer margins */
                box-shadow: none !important;
                border: none !important;
                background-color: white !important;
                text-align: left !important;
            }

            /* Add a title for the printed page */
            body::before {
                content: "PAYSLIP DETAILS";
                display: block;
                text-align: center;
                font-size: 14pt;
                margin-top: 1rem;
                margin-bottom: 2rem;
                font-weight: bold;
                visibility: visible !important;
            }

            /* --- PAYSLIP LAYOUT FIXES --- */
            #temp-print-content .row {
                overflow: hidden !important; /* Clearfix for floated columns */
            }

            #temp-print-content .row > .col-md-6:nth-of-type(1),
            #temp-print-content .row > .col-md-6:nth-of-type(2) {
                float: left !important;
                width: 50% !important;
            }
            #temp-print-content .row > .col-md-6:nth-of-type(2) {
                text-align: right !important;
            }

            #temp-print-content .row > .col-md-12 {
                clear: both !important;
                float: none !important;
                width: 100% !important;
                // margin-top: 1.5rem !important;
            }

            /* --- TABLE STYLING --- */
            .table-responsive {
                overflow: visible !important;
            }

            #temp-print-content table.table-sm {
                width: 100% !important;
                border: 1px solid #000 !important;
            }

            #temp-print-content .table-sm th {
                text-align: left !important;
                padding: 2px 3px !important;
                border-bottom: 1px solid #000 !important;
            }

            #temp-print-content .table-sm td {
                padding: 2px 3px !important;
                border: 1px solid #000 !important;
            }

            #temp-print-content .table-sm th:nth-child(1),
            #temp-print-content .table-sm td:nth-child(1) { width: 30% !important; }

            #temp-print-content .table-sm th:nth-child(2),
            #temp-print-content .table-sm td:nth-child(2) { width: 20% !important; text-align: right !important; }

            #temp-print-content .table-sm th:nth-child(3),
            #temp-print-content .table-sm td:nth-child(3) { width: 30% !important; padding-left: 1.5rem !important; }

            #temp-print-content .table-sm th:nth-child(4),
            #temp-print-content .table-sm td:nth-child(4) { width: 20% !important; text-align: right !important; }

            /* --- TABLE FOOTER (SUMMARY) STYLING --- */
            #temp-print-content .table-sm tfoot td {
                border-top: 1px solid #000 !important;
                border: none !important;
                padding: 2px 3px !important;
                text-align: right;
            }
            #temp-print-content .table-sm tfoot tr:first-child td {
                padding-top: 1px !important;
            }
        }
    `);$("head").append(p);const l=new Date,g=l.getFullYear()+"-"+(l.getMonth()+1).toString().padStart(2,"0")+"-"+l.getDate().toString().padStart(2,"0"),h=document.title,y=`${g}-payslip-${t.name}-Tinatangi-Cafe`;document.title=y;const n=()=>{window.removeEventListener("focus",n),p.remove(),d.remove(),document.title=h};window.addEventListener("focus",n,{once:!0}),window.matchMedia("print").addListener(o=>{o.matches||n()}),setTimeout(n,500),window.print()}export{x as b,f as p};
