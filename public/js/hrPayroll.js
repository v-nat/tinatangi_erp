import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    $("#payrollsTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/human-resources/payroll/list",
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
                width: "45px",
            },
            { data: "name", className: "dt-left" },
            { data: "department", className: "dt-left" },
            { data: "position", className: "dt-left" },
            {
                data: "period",
                className: "dt-left",
                // render: function (data) {
                //     return data ? formatDate(data) : "N/A";
                // },
                // type: "date", // Ensure proper date sorting
            },
            {
                data: "reg_pay",
                className: "dt-left",
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
                className: "dt-left",
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
                className: "dt-left",
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
                className: "dt-left",
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
                width: "150px",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (
                        row.status ==
                        '<span class="badge bg-success">Approved</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            data-employee-id="${row.employee_id}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-success bs-tooltip me-2 release-btn"
                                data-id="${data}"
                            data-employee-id="${row.employee_id}"
                                title="Release">
                                    <i class="fa-solid fa-check"></i>
                            </a>
                        </div>
                        `;
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            data-employee-id="${row.employee_id}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                        `;
                    }
                },
                className: "text-center",
                width: "150px",
            },
        ],
    });
    $(document).on("click", ".btn-view", function () {
        const payroll_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(
            `/human-resources/payroll/view/${payroll_id}`,
            function (response) {
                buildPayslipModal(response.data);
            }
        ).fail(function () {
            alert("Failed to load payslip.");
        });
    });

    $(document).on("click", ".release-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        Swal.fire({
            title: "Release Payroll?",
            text: "You are about to release this employee's payroll.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm!",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: `/human-resources/payroll/release/${id}`,
                type: "PUT",
                data: null,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("payrollsTable");
                    Toast.fire({
                        text: response.message,
                        icon: "success",
                    });
                },
                error: function (xhr) {
                    // console.error('Error response:', xhr);
                    $("#LoadingScreen").fadeOut(200);
                    if (xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(
                            xhr.responseJSON.errors
                        )
                            .flat()
                            .join("\n");
                        Toast.fire("Validation Error", errorMessages, "error");
                    } else {
                        Toast.fire(
                            "Error",
                            "An unexpected error occurred.",
                            "error"
                        );
                    }
                },
            });
        });
    });

    function buildPayslipModal(data) {
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
});
