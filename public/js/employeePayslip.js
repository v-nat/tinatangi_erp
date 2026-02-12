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
            },
            {
                data: "reg_pay",
                className: "dt-left",
                render: function (data) {
                    return "₱ " + parseFloat(data).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
            },
            {
                data: "gross_pay",
                className: "dt-left",
                render: function (data) {
                    return "₱ " + parseFloat(data).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
            },
            {
                data: "gross_deduction",
                className: "dt-left",
                render: function (data) {
                    return "₱ " + parseFloat(data).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
            },
            {
                data: "net_pay",
                className: "dt-left",
                render: function (data) {
                    return "₱ " + parseFloat(data).toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
                    if (row.status.includes("Released")) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-print-payslip bs-tooltip me-2"
                            data-id="${data}"
                            data-proof="${row.proof_of_payment || ''}"
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

    $(document).on("click", ".btn-print-payslip", function (e) {
        e.preventDefault();
        const $btn = $(this);
        const payroll_id = $btn.data("id");
        const proof = $btn.attr("data-proof"); // Use .attr to get the raw string value

        // CONDITIONAL LOGIC:
        // If proof exists (is not empty/null), print directly.
        // Otherwise, show the modal to upload proof.
        if (proof && proof !== "" && proof !== "null" && proof !== undefined) {
            $("#LoadingScreen").fadeIn(200);
            $.get(`/employee/payslip/data/${payroll_id}`, function (response) {
                printPayslip(response.data);
                $("#LoadingScreen").fadeOut(200);
            }).fail(function () {
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to load payslip data for printing."
                });
            });
        } else {
            // No proof found: Show the modal for acknowledgement
            $("#proofOfPaymentForm")[0].reset();
            $("#proof_payroll_id").val(payroll_id);
            $("#proofOfPaymentModal").modal("show");
        }
    });

    // Handle Proof Submission
    $("#btn-submit-proof").on("click", function () {
        const fileInput = $("#proof_file")[0];
        const payrollId = $("#proof_payroll_id").val();

        if (fileInput.files.length === 0) {
            Toast.fire({
                icon: "warning",
                title: "Warning",
                text: "Please upload an image as proof of payment."
            });
            return;
        }

        const formData = new FormData();
        formData.append("proof", fileInput.files[0]);
        formData.append("payroll_id", payrollId);

        const $btn = $(this);
        const originalText = $btn.html();
        $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Uploading...').prop("disabled", true);

        $.ajax({
            url: "/employee/payslip/acknowledge",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $("#proofOfPaymentModal").modal("hide");

                // IMPORTANT: Reload table to ensure the 'data-proof' attribute is updated
                payslipTable.ajax.reload(null, false);

                // Fetch data and print immediately after successful upload
                $.get(`/employee/payslip/data/${payrollId}`, function (response) {
                    printPayslip(response.data);
                }).fail(function () {
                    Toast.fire({
                        icon: "error",
                        title: "Error",
                        text: "Proof submitted, but failed to load payslip for printing."
                    });
                });

                $btn.html(originalText).prop("disabled", false);
            },
            error: function (xhr) {
                Toast.fire({
                    icon: "error",
                    title: "Error",
                    text: xhr.responseJSON?.message || "Failed to upload proof. Please try again."
                });
                $btn.html(originalText).prop("disabled", false);
            }
        });
    });

    $(document).on("click", ".btn-view", function () {
        const payroll_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/employee/payslip/data/${payroll_id}`, function (response) {
            buildPayslipModal(response.data);
            $("#LoadingScreen").fadeOut(200);
        }).fail(function () {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire({
                icon: "error",
                title: "Error",
                text: "Failed to load payslip."
            });
        });
    });

    $("#btn-refresh-employee-payslips").on("click", function () {
        payslipTable.ajax.reload(null, false);
    });
});
