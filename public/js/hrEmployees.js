import { formatDate2, formatDateString } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const employeeTable = $("#employee_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/human-resources/employees/get",
            type: "GET",
            dataSrc: "data",
            error: function (xhr, error, thrown) {
                console.error("Ajax error:", error, thrown);
                alert(
                    "Error loading employee data. Check the console and ensure the AJAX URL is correct."
                );
            },
        },
        columns: [
            {
                data: "employee_id",
                render: function (data, type, row) {
                    return `<input class="form-check-input employee-checkbox" type="checkbox" value="${data}">`;
                },
                className: "text-center",
                orderable: false,
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

        initComplete: function () {
            const column = this.api().column(3);
            const select = $("#department_filter");

            column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        select.append(
                            $("<option></option>").attr("value", d).text(d)
                        );
                    }
                });
        },
    });

    $("#department_filter").on("change", function () {
        const selectedDepartment = $(this).val();

        employeeTable
            .column(3)
            .search(
                selectedDepartment ? "^" + selectedDepartment + "$" : "",
                true,
                false
            )
            .draw();
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

    $("#payroll_start_date").on("change", function () {
        const startDateStr = $(this).val();
        if (!startDateStr) {
            $("#payroll_end_date").prop("min", "");
            return;
        }

        try {
            const startDate = new Date(startDateStr);

            const minEndDate = new Date(startDate.getTime());
            minEndDate.setDate(minEndDate.getDate() + 14);

            const minEndDateStr = minEndDate.toISOString().split("T")[0];

            $("#payroll_end_date").prop("min", minEndDateStr);

            const endDateStr = $("#payroll_end_date").val();
            if (endDateStr) {
                const endDate = new Date(endDateStr);
                if (endDate < minEndDate) {
                    $("#payroll_end_date").val("");
                    Toast.fire({
                        text: "End date was cleared as it must be at least 15 days after the start date.",
                        icon: "error",
                    });
                }
            }
        } catch (e) {
            Toast.fire("Invalid start date", e);
            $("#payroll_end_date").prop("min", "");
        }
    });

    $("#payroll_end_date").on("change", function () {
        const endDateStr = $(this).val();
        const startDateStr = $("#payroll_start_date").val();

        if (!endDateStr || !startDateStr) {
            return;
        }

        try {
            const startDate = new Date(startDateStr);
            const endDate = new Date(endDateStr);

            const minEndDate = new Date(startDate.getTime());
            minEndDate.setDate(minEndDate.getDate() + 14);

            if (endDate < minEndDate) {
                Toast.fire({
                    text: "End date must be at least 15 days after the start date.",
                    icon: "error",
                });
                $(this).val("");
            }
        } catch (e) {
            Toast.fire("Invalid date comparison", e);
        }
    });

    $("#select_all_employees").on("change", function () {
        const isChecked = $(this).is(":checked");
        employeeTable
            .rows({ page: "current" })
            .nodes()
            .to$()
            .find(".employee-checkbox")
            .prop("checked", isChecked);
    });

    $("#employee_table tbody").on("change", ".employee-checkbox", function () {
        if (!$(this).is(":checked")) {
            $("#select_all_employees").prop("checked", false);
        } else {
            const allCheckedOnPage =
                employeeTable
                    .rows({ page: "current" })
                    .nodes()
                    .to$()
                    .find(".employee-checkbox:not(:checked)").length === 0;
            $("#select_all_employees").prop("checked", allCheckedOnPage);
        }
    });

    employeeTable.on("page.dt", function () {
        $("#select_all_employees").prop("checked", false);
    });

    $("#select_all_employees").on("change", function () {
        const isChecked = $(this).is(":checked");
        employeeTable
            .rows({ page: "current" })
            .nodes()
            .to$()
            .find(".employee-checkbox")
            .prop("checked", isChecked);
    });

    $("#employee_table tbody").on("change", ".employee-checkbox", function () {
        if (!$(this).is(":checked")) {
            $("#select_all_employees").prop("checked", false);
        } else {
            const allCheckedOnPage =
                employeeTable
                    .rows({ page: "current" })
                    .nodes()
                    .to$()
                    .find(".employee-checkbox:not(:checked)").length === 0;
            $("#select_all_employees").prop("checked", allCheckedOnPage);
        }
    });

    employeeTable.on("page.dt", function () {
        $("#select_all_employees").prop("checked", false);
    });

    $("#batch_payroll_btn").on("click", function () {
        const selectedEmployeeIds = [];

        employeeTable
            .rows()
            .nodes()
            .to$()
            .find(".employee-checkbox:checked")
            .each(function () {
                selectedEmployeeIds.push($(this).val());
            });

        const startDate = $("#payroll_start_date").val();
        const endDate = $("#payroll_end_date").val();

        if (selectedEmployeeIds.length === 0) {
            Toast.fire({
                text: "No employees selected!",
                icon: "error",
            });
            return;
        }

        if (!startDate || !endDate) {
            Toast.fire({
                text: "Please select a start and end date for the payroll period.",
                icon: "warning",
            });
            return;
        }

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
                $("#batch_payroll_btn")
                    .prop("disabled", true)
                    .html(
                        '<i class="fas fa-spinner fa-spin"></i> Processing...'
                    );
                $.ajax({
                    url: "/human-resources/payroll/batch-generate",
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    data: {
                        employee_ids: selectedEmployeeIds,
                        start_date: startDate,
                        end_date: endDate,
                    },
                    success: function (response) {
                        Toast.fire(
                            "Success!",
                            "Batch payroll generation has started.",
                            "success"
                        );
                        $("#payroll_start_date").val("");
                        $("#payroll_end_date").val("");
                        $("#payroll_end_date").prop("min", "");

                        $("#select_all_employees").prop("checked", false);
                        employeeTable
                            .rows()
                            .nodes()
                            .to$()
                            .find(".employee-checkbox")
                            .prop("checked", false);

                        employeeTable.draw(false);
                        $("#batch_payroll_btn")
                            .prop("disabled", false)
                            .html("Generate Batch Payroll");
                    },
                    error: function (xhr) {
                        console.error("Batch payroll error:", xhr.responseText);
                        Toast.fire(
                            "Error",
                            "Could not start batch payroll.",
                            "error"
                        );
                    },
                });
            }
        });
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
                        submitBtn
                            .prop("disabled", false)
                            .html("Generate Payroll");
                    },
                });
            }
        });
    });
});
