/**
 * Builds payslip HTML from data and prints it directly without a modal.
 * This function constructs the payslip, applies print-specific CSS,
 * opens the print dialog, and then cleans up the temporary elements.
 * @param {object} data - The payslip data object.
 */
export function printPayslip(data) {
    // 1. Prepare data for the combined table
    const earnings = [
        { label: "Regular Pay", value: data.reg_pay },
        { label: "Overtime Pay", value: data.overtime_pay },
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
                        ? `<td class="text-bold-500">${
                              earning.label
                          }</td><td>₱ ${parseFloat(
                              earning.value
                          ).toLocaleString()}</td>`
                        : "<td></td><td></td>"
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

    // 2. Build the Payslip HTML content with the new single table format and integrated footer.
    const payslipHtml = `
        <div class="payslip-print-container">
            <div class="row mb-4 px-48">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Employee: ${data.name}</strong></p>
                    <p class="mb-0">Department: ${data.department}</p>
                    <p class="mb-0">Position: ${data.position}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1"><strong>Pay Period: ${data.start_date} - ${
        data.end_date
    }</strong></p>
                    <p class="mb-1">Working Days: ${data.working_days}</p>
                    <p class="mb-1">Days Present: ${data.days_present}</p>
                </div>

                <!-- Combined Earnings and Deductions Table -->
                <div class="col-md-12 mt-5">
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
                                    <td class="text-bold-500">Gross Pay:</td>
                                    <td><strong>₱ ${parseFloat(
                                        data.gross_pay
                                    ).toLocaleString()}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-500">Gross Deduction:</td>
                                    <td><strong>₱ ${parseFloat(
                                        data.gross_deduction
                                    ).toLocaleString()}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-500">Salary Before Tax:</td>
                                    <td><strong>₱ ${parseFloat(
                                        data.salary_before_tax
                                    ).toLocaleString()}</strong></td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                    <td class="text-bold-500">Net Pay:</td>
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

    // 3. Create a temporary container for the print content.
    const $printContent = $('<div id="temp-print-content"></div>').html(
        payslipHtml
    );
    $("body").append($printContent);

    // 4. Define Print-Specific CSS Styles
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
                margin-top: 1.5rem !important;
            }

            /* --- TABLE STYLING --- */
            .table-responsive {
                overflow: visible !important;
            }

            #temp-print-content table.table-sm {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-top: 1.5rem !important;
            }

            #temp-print-content .table-sm th {
                text-align: left !important;
                padding: 5px 6px !important;
                border-bottom: 2px solid #000 !important;
            }

            #temp-print-content .table-sm td {
                padding: 4px 6px !important;
                border-bottom: 1px solid #ddd;
            }

            #temp-print-content .table-sm tbody tr:last-child td {
                 border-bottom: none !important;
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
                border-top: 2px solid #000 !important;
                border-bottom: none !important;
                padding: 5px 6px !important;
                text-align: right;
                font-weight: bold;
            }
            #temp-print-content .table-sm tfoot tr:first-child td {
                padding-top: 10px !important;
            }
        }
    `;

    // 5. Apply Styles and Trigger Print
    const $printStyleElement = $(
        '<style type="text/css" id="print-temp-style">'
    ).text(printStyles);
    $("head").append($printStyleElement);

    // 6. Define Cleanup Logic
    const cleanupAndRestore = () => {
        window.removeEventListener("focus", cleanupAndRestore);
        $printStyleElement.remove();
        $printContent.remove();
    };

    // 7. Set up Event Listeners for Cleanup
    window.addEventListener("focus", cleanupAndRestore, { once: true });
    const mediaQueryList = window.matchMedia("print");
    mediaQueryList.addListener((mql) => {
        if (!mql.matches) {
            cleanupAndRestore();
        }
    });
    setTimeout(cleanupAndRestore, 500);

    // 8. Initiate the Print Dialog
    window.print();
}

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
