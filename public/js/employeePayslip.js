
import { printPayslip, buildPayslipModal } from "./utils/printPayslip.js";

$(document).ready(function () {
    const id = $("#employee_id").data("id").toString();
    const payslipTable = $("#employeePayslipTable").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/employee/payslip/list/" + id,
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
                        '<span class="badge bg-success">Released</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-print-payslip bs-tooltip me-2"
                            data-id="${data}"
                            data-employee-id="${row.employee_id}"
                            title="Print Payslip">
                                <i class="fa-solid fa-money-check-dollar"></i>
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
    $(document).on("click", ".btn-print-payslip", function () {
        const payroll_id = $(this).data("id");
        $.get(`/employee/payslip/data/${payroll_id}`, function (response) {
            printPayslip(response.data);
        }).fail(function () {
            alert("Failed to load payslip.");
        });
    });
    $(document).on("click", ".btn-view", function () {
        const payroll_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/employee/payslip/data/${payroll_id}`, function (response) {
            buildPayslipModal(response.data);
        }).fail(function () {
            alert("Failed to load payslip.");
        });
    });

    $("#btn-refresh-employee-payslips").on("click", function () {
        payslipTable.ajax.reload(null, false);
    });
});
