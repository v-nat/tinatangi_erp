export function buildPayslipModal(data) {
    const html = `
        <div class="row mb-4 px-48">
            ${data.remarks}
            <div class="col-md-6">
                <h6 class="mb-1">Employee: <strong>${data.name}</strong></h6>
                <p class="mb-0">Department: ${data.department}</p>
                <p class="mb-0">Position: ${data.position}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="mb-1">Pay Period: <strong>${data.start_date} - ${
        data.end_date
    }</strong></h6>
                <h6 class="mb-1">Working Days: ${data.working_days}</h6>
                <h6 class="mb-1">Days Present: ${data.days_present}</h6>
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
                                <td class="text-bold-500">Total Hours Pay</td>
                                <td>${data.total_hours} hours</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Regular Pay</td>
                                <td>₱ ${parseFloat(
                                    data.reg_pay
                                ).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Overtime Pay</td>
                                <td>₱ ${parseFloat(
                                    data.overtime_pay
                                ).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Leave Pay</td>
                                <td>₱ ${parseFloat(
                                    data.leave_pay
                                ).toLocaleString()}</td>
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
                                <td>₱ ${parseFloat(
                                    data.absent_deduction
                                ).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Tardiness Deduction</td>
                                <td>₱ ${parseFloat(
                                    data.tardiness_deduction
                                ).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">SSS</td>
                                <td>₱ ${parseFloat(
                                    data.sss
                                ).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Pagibig</td>
                                <td>₱ ${parseFloat(
                                    data.pagibig
                                ).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Philhealth</td>
                                <td>₱ ${parseFloat(
                                    data.philhealth
                                ).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Tax</td>
                                <td>₱ ${parseFloat(
                                    data.tax_deduction
                                ).toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
            </div>
            <div class="col-md-6 text-md-start">
                <h6 class="mb-1">Gross Pay: <strong>₱ ${parseFloat(
                    data.gross_pay
                ).toLocaleString()}</strong></h6>
                <h6 class="mb-1">Gross Deduction: <strong>₱ ${parseFloat(
                    data.gross_deduction
                ).toLocaleString()}</strong></h6>
                <h6 class="mb-1">Salary Before Tax: <strong>₱ ${parseFloat(
                    data.salary_before_tax
                ).toLocaleString()}</strong></h6>
                <br>
                <h6 class="mb-1">Net Pay: <strong>₱ ${parseFloat(
                    data.net_pay
                ).toLocaleString()}</strong></h6>
            </div>
        </div>
        <style>
        .table-sm td,
        .table-sm th {
            padding: 0.4rem 0.6rem; /* tighter vertical and horizontal spacing */
            font-size: 0.875rem;    /* slightly smaller text */
        }
        </style>
    `;
    $("#LoadingScreen").fadeOut(200);
    $("#viewPayroll .modal-body").html(html);
    $("#viewPayroll").modal("show");
}

/**
 * Builds payslip HTML from data and prints it directly without a modal.
 * This function constructs the payslip, applies print-specific CSS,
 * opens the print dialog, and then cleans up the temporary elements.
 * @param {object} data - The payslip data object.
 */
export function printPayslip(data) {
    const earnings = [
        { label: "Working Days", days: data.working_days },
        { label: "Days Present", days: data.days_present },
        { label: "Total Hours", hours: data.total_hours },
        { label: "Regular Pay", value: data.reg_pay },
        { label: "Overtime Pay", value: data.overtime_pay },
        { label: "Leave Pay", value: data.leave_pay },
    ];

    const deductions = [
        { label: "Days Absent Deduction", value: data.absent_deduction },
        { label: "Tardiness Deduction", value: data.tardiness_deduction },
        { label: "SSS", value: data.sss },
        { label: "Pagibig", value: data.pagibig },
        { label: "Philhealth", value: data.philhealth },
        { label: "Tax", value: data.tax_deduction },
    ];

    let tableRowsHtml = "";
    const numRows = Math.max(earnings.length, deductions.length);

    for (let i = 0; i < numRows; i++) {
        const earning = earnings[i];
        const deduction = deductions[i];

        tableRowsHtml += `
            <tr>
                ${
                    earning
                        ? earning.value
                            ? // If earning.value exists
                              `<td class="text-bold-500">${
                                  earning.label
                              }</td><td>₱ ${parseFloat(
                                  earning.value
                              ).toLocaleString()}</td>`
                            : earning.days
                            ? // Else, if earning.days exists
                              `<td class="text-bold-500">${earning.label}</td><td>${earning.days} days</td>`
                            : earning.hours
                            ? // Else, if earning.hours exists
                              `<td class="text-bold-500">${earning.label}</td><td>${earning.hours} hours</td>`
                            : // Else, if earning exists but has none of the above
                              "<td></td><td></td>"
                        : // Else, if earning does not exist
                          "<td></td><td></td>"
                }
                ${
                    deduction
                        ? `<td class="text-bold-500">${
                              deduction.label
                          }</td><td>₱ ${parseFloat(
                              deduction.value
                          ).toLocaleString()}</td>`
                        : "<td></td><td></td>"
                }
            </tr>
        `;
    }

    const payslipHtml = `
        <div class="payslip-print-container">
            <div class="row d-flex justify-content-center align-items-center mb-3">
                <div class="col-6 col-md-6">
                    <img src="/tinatangilogo2 - Copy.png" style="width:auto; height: 40px !important;" alt="Tinatangi Logo">
                </div>
                <div class="col-6 col-md-6 text-end">
                    <p class="mb-0">${new Date().toLocaleString("en-US", {
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                    })}</p>
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
                        <p class="mb-0">Employee: ${data.name}</p>
                        <p class="mb-0">Department: ${data.department}</p>
                        <p class="mb-0">Position: ${data.position}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">Pay Period: ${data.period}</p>
                        <p class="mb-0">Starting Date: ${data.start_date}</p>
                        <p class="mb-0">End Date: ${data.end_date}</p>
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
                                ${tableRowsHtml}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Gross Pay:</td>
                                    <td>₱ ${parseFloat(
                                        data.gross_pay
                                    ).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Gross Deduction:</td>
                                    <td>₱ ${parseFloat(
                                        data.gross_deduction
                                    ).toLocaleString()}</td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Salary Before Tax:</td>
                                    <td>₱ ${parseFloat(
                                        data.salary_before_tax
                                    ).toLocaleString()}</td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-100">Net Pay:</td>
                                    <td><strong>₱ ${parseFloat(
                                        data.net_pay
                                    ).toLocaleString()}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;

    const $printContent = $('<div id="temp-print-content"></div>').html(
        payslipHtml
    );
    $("body").append($printContent);

    const printStyles = `
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
    `;

    const $printStyleElement = $(
        '<style type="text/css" id="print-temp-style">'
    ).text(printStyles);
    $("head").append($printStyleElement);

    const cleanupAndRestore = () => {
        window.removeEventListener("focus", cleanupAndRestore);
        $printStyleElement.remove();
        $printContent.remove();
    };

    window.addEventListener("focus", cleanupAndRestore, { once: true });
    const mediaQueryList = window.matchMedia("print");
    mediaQueryList.addListener((mql) => {
        if (!mql.matches) {
            cleanupAndRestore();
        }
    });
    setTimeout(cleanupAndRestore, 500);

    window.print();
}
