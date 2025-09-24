$(document).ready(function () {
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }

    $("#payrollsTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/humanresources/payroll/list",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
            },
            { data: "name" },
            { data: "department" },
            { data: "position" },
            {
                data: "period",
                // render: function (data) {
                //     return data ? formatDate(data) : "N/A";
                // },
                // type: "date", // Ensure proper date sorting
            },
            {
                data: "reg_pay",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            {
                data: "gross_pay",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            {
                data: "gross_deduction",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            {
                data: "net_pay",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    return `
                    <div class="action-btns">
                        <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                           data-id="${data}"
                           data-employee-id="${row.employee_id}"
                           title="View">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 approve-btn"
                            data-id="${data}"
                           data-employee-id="${row.employee_id}"
                            title="Approve">
                                <i class="fa-solid fa-check"></i>
                        </a>
                        <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                            data-id="${data}"
                           data-employee-id="${row.employee_id}"
                            title="Reject">
                                <i class="fa-solid fa-x"></i>
                        </a>
                    </div>
                        `;
                },
            },
        ],
    });
    $(document).on("click", ".btn-view", function () {
        const payroll_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(
            `/humanresources/payroll/view/${payroll_id}`,
            function (response) {
                buildPayslipModal(response.data);
            }
        ).fail(function () {
            alert("Failed to load payslip.");
        });
    });

    function buildPayslipModal(data) {

        const html = `
        <div class="row mb-4 px-48">
            <div class="col-md-6">
                <h6 class="mb-1">Employee: <strong>${data.name}</strong></h6>
                <p class="mb-0">Department: ${data.department}</p>
                <p class="mb-0">Position: ${data.position}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="mb-1">Pay Period: <strong>${data.start_date} - ${data.end_date}</strong></h6>
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
                                <td>₱ ${parseFloat(data.reg_pay).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Overtime Pay</td>
                                <td>₱ ${parseFloat(data.overtime_pay).toLocaleString()}</td>
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
                                <td>₱ ${parseFloat(data.absent_deduction).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Tardiness Deduction</td>
                                <td>₱ ${parseFloat(data.tardiness_deduction).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">SSS</td>
                                <td>₱ ${parseFloat(data.sss).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Pagibig</td>
                                <td>₱ ${parseFloat(data.pagibig).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Philhealth</td>
                                <td>₱ ${parseFloat(data.philhealth).toLocaleString()}</td>
                            </tr>
                            <tr>
                                <td class="text-bold-500">Tax</td>
                                <td>₱ ${parseFloat(data.tax_deduction).toLocaleString()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
            </div>
            <div class="col-md-6 text-md-start">
                <h6 class="mb-1">Gross Pay: <strong>₱ ${parseFloat(data.gross_pay).toLocaleString()}</strong></h6>
                <h6 class="mb-1">Gross Deduction: <strong>₱ ${parseFloat(data.gross_deduction).toLocaleString()}</strong></h6>
                <h6 class="mb-1">Salary Before Tax: <strong>₱ ${parseFloat(data.salary_before_tax).toLocaleString()}</strong></h6>
                <br>
                <h6 class="mb-1">Net Pay: <strong>₱ ${parseFloat(data.net_pay).toLocaleString()}</strong></h6>
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
});
