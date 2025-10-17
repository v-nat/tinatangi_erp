import { formatDate2, formatDateString } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    $("#employee_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/human-resources/employees/get",
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
            { data: "position", className: "dt-left" },
            { data: "department", className: "dt-left" },
            { data: "email", className: "dt-left" },
            { data: "direct_supervisor", className: "dt-left" },
            {
                data: "status",
                render: function (data) {
                    return `<span class="badge bg-success ${
                        data === "active" ? "success" : "danger"
                    }">${data}</span>`;
                },
                className: "text-center",
                width: "150px",
            },
            {
                data: "employee_id",
                render: function (data, type, row) {
                    return `
                    <div class="action-btns">
                        <a href="#" class="btn icon btn-primary btn-edit bs-tooltip me-2"
                           data-id="${data}"
                           data-name="${row.name}"
                           data-employee-id="${row.employee_id}"
                           title="Edit">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <a href="#" class="btn icon btn-info generate-payroll bs-tooltip me-2"
                           title="Generate Payroll"
                           data-id="${data}"
                           data-name="${row.name}">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </a>
                    </div>
                        `;
                },
                width: "150px",
                className: "text-center",
            },
        ],
    });

    $(document).on("click", ".btn-edit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        window.location.href = "/human-resources/edit-employee/" + id;
    });
    $(document).on("click", ".generate-payroll", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const name = $(this).data("name");
        $("#empId").text(id);
        $("#empName").text(name);
        $("#modalEmployeeId").val(id);

        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        $("#start_date").val(formatDate2(firstDay));
        $("#end_date").val(formatDate2(lastDay));

        $("#generatePayroll").modal("show");
    });

    $("#payrollForm").on("submit", function (e) {
        e.preventDefault();
        $("#generatePayroll").modal("hide");
        const form = $(this);
        const formData = form.serialize();
        const submitBtn = form.find('button[type="submit"]');
        const startDate = formatDateString($("#start_date").val());
        const endDate = formatDateString($("#end_date").val());

        Swal.fire({
            title: "Confirm Payroll Generation",
            html: `<div class="text-left">
                    <ul class="list-unstyled">
                        <li>From: ${startDate}</li>
                        <li>To: ${endDate}</li>
                    </ul>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Please verify the dates before proceeding.</p>
                </div>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Generate Payroll",
            cancelButtonText: "Cancel",
            width: "500px",
        }).then((result) => {
            if (result.isConfirmed) {
                submitBtn
                    .prop("disabled", true)
                    .html(
                        '<i class="fas fa-spinner fa-spin"></i> Processing...'
                    );

                $.ajax({
                    url: form.attr("action"),
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            $("#generatePayrollModal").modal("hide");

                            Toast.fire({
                                text: "Payroll Generated Successfully!",
                                icon: "success",
                            }).then(
                                () =>
                                    (window.location.href =
                                        "/human-resources/payroll")
                            );
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = "Failed to generate payroll";
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }

                        Toast.fire({
                            title: "Error!",
                            text: errorMessage,
                            icon: "error",
                        });
                    },
                    complete: function () {
                        // Re-enable button
                        submitBtn
                            .prop("disabled", false)
                            .html("Generate Payroll");
                    },
                });
            }
        });
    });
});
