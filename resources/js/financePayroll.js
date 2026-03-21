import $ from 'jquery';
import Swal from 'sweetalert2';
import { reloadTable } from "./utils/reloadTable.js";
import { printPayslip, buildPayslipModal } from "./utils/printPayslip.js";

let currentPayrollId = null;

$(document).ready(function () {
    const payrollsTable = $("#payrollsTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/finance/payroll/list",
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
            { data: "period", className: "dt-left" },
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
                            <a href="#" class="btn icon btn-sm btn-info btn-print-payslip bs-tooltip"
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
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip"
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
        initComplete: function () {
            const deptColumn = this.api().column(2);
            const deptSelect = $('#payroll_department_filter');
            deptColumn.data().unique().sort().each(function (d, j) {
                if (d) {
                    deptSelect.append($('<option></option>').attr('value', d).text(d));
                }
            });

            const periodColumn = this.api().column(4);
            const periodSelect = $('#payroll_period_filter');
            periodColumn.data().unique().sort().each(function (d, j) {
                if (d) {
                    periodSelect.append($('<option></option>').attr('value', d).text(d));
                }
            });
        }
    });

    $("#refresh-payrolls").on("click", function () {
        payrollsTable.ajax.reload();
    });

    $("#payroll_department_filter").on("change", function () {
        const selectedDept = $(this).val();
        payrollsTable.column(2).search(
            selectedDept ? '^' + selectedDept + '$' : '',
            true,
            false
        ).draw();
    });

    $("#payroll_period_filter").on("change", function () {
        const selectedPeriod = $(this).val();
        payrollsTable.column(4).search(
            selectedPeriod ? '^' + selectedPeriod + '$' : '',
            true,
            false
        ).draw();
    });

    $(document).on("click", ".btn-view", function () {
        const payroll_id = $(this).data("id");
        currentPayrollId = payroll_id;
        $("#LoadingScreen").fadeIn(200);

        $.get(`/finance/payroll/view/${payroll_id}`, function (response) {
            buildPayslipModal(response.data);

            if (response.data.status_id == 11) {
                $("#modal-approve-btn").removeClass("d-none");
                $("#modal-reject-btn").removeClass("d-none");
            } else {
                $("#modal-approve-btn").addClass("d-none");
                $("#modal-reject-btn").addClass("d-none");
            }
        }).fail(function () {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire("Error", "Failed to load payslip.", "error");
        });
    });

    $(document).on("click", ".btn-print-payslip", function () {
        const payroll_id = $(this).data("id");
        $.get(`/finance/payroll/view/${payroll_id}`, function (response) {
            printPayslip(response.data);
        }).fail(function () {
            Toast.fire("Error", "Failed to load payslip.", "error");
        });
    });

    $("#modal-approve-btn").on("click", function () {
        Swal.fire({
            title: "Process Payroll?",
            text: "You are about to put on process this employee's payroll.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm!",
        }).then((result) => {
            if (!result.isConfirmed) return;

            $("#viewPayroll").modal("hide");
            $("#LoadingScreen").fadeIn(200);

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                url: `/finance/payroll/process/${currentPayrollId}/14`,
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
                    $("#LoadingScreen").fadeOut(200);
                    if (xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(xhr.responseJSON.errors)
                            .flat()
                            .join("\n");
                        Toast.fire("Validation Error", errorMessages, "error");
                    } else {
                        Toast.fire("Error", "An unexpected error occurred.", "error");
                    }
                },
            });
        });
    });

    $("#modal-reject-btn").on("click", function () {
        $("#rejectionPayrollId").val(currentPayrollId);
        $("#payrollRejectionNotes").val("");
        $("#viewPayroll").modal("hide");
        $("#payrollRejectionModal").modal("show");
    });

    $("#payroll-reject-btn-confirmed").on("click", function () {
        const remarks = $("#payrollRejectionNotes").val().trim();
        const id = $("#rejectionPayrollId").val();

        if (!remarks) {
            Toast.fire("Error", "Please provide a rejection reason.", "error");
            return;
        }

        $("#payrollRejectionModal").modal("hide");
        $("#LoadingScreen").fadeIn(200);

        $.ajax({
            url: `/finance/payroll/process/${id}/12`,
            method: "PUT",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id: id,
                remarks: remarks,
            },
            success: function (response) {
                if (response.success) {
                    $("#LoadingScreen").fadeOut(200);
                    reloadTable("payrollsTable");
                    Toast.fire("Rejected!", response.message, "success");
                } else {
                    Toast.fire("Error", response.message, "error");
                }
            },
            error: function (xhr) {
                Toast.fire(
                    "Error",
                    xhr.responseJSON?.message || "Something went wrong",
                    "error"
                );
            },
        });
    });

    $("#viewPayroll").on("hidden.bs.modal", function () {
        currentPayrollId = null;
        $("#modal-approve-btn").addClass("d-none");
        $("#modal-reject-btn").addClass("d-none");
    });
});
