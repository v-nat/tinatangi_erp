import { reloadTable } from "./utils/reloadTable.js";
import { printPayslip, buildPayslipModal } from "./utils/printPayslip.js";

$(document).ready(function () {
    let positionsTable = null;
    let originalContributions = {};
    let originalBaseSalary = null;
    const WORKING_DAYS_PER_MONTH = 26;
    const WORKING_HOURS_PER_DAY = 8;

    function formatToCurrency(value) {
        if (value === null || value === undefined || value === "") return "";
        const number = Number(String(value).replace(/[₱,]/g, ""));
        if (isNaN(number) || !isFinite(number)) return "";
        return new Intl.NumberFormat("en-PH", {
            style: "currency",
            currency: "PHP",
        }).format(number);
    }

    function formatToNumber(value) {
        if (typeof value !== "string") return value;
        return value.replace(/[₱,]/g, "").trim();
    }

    $("body")
        .on("focus", 'input[inputmode="decimal"]', function () {
            if (!$(this).is("[readonly]")) {
                $(this).val(formatToNumber($(this).val()));
            }
        })
        .on("blur", 'input[inputmode="decimal"]', function () {
            if (!$(this).is("[readonly]")) {
                $(this).val(formatToCurrency($(this).val()));
            }
            if ($(this).attr("id") === "edit_base_salary") {
                calculateAndUpdateRates();
            }
        });

    function calculateRates(baseSalary) {
        const base = parseFloat(baseSalary) || 0;
        let ratePerHour = 0;
        let ratePerDay = 0;
        if (
            base > 0 &&
            WORKING_DAYS_PER_MONTH > 0 &&
            WORKING_HOURS_PER_DAY > 0
        ) {
            ratePerDay = base / WORKING_DAYS_PER_MONTH;
            ratePerHour = ratePerDay / WORKING_HOURS_PER_DAY;
        }
        return {
            rate_per_hour: ratePerHour,
            rate_per_day: ratePerDay,
        };
    }

    function calculateAndUpdateRates() {
        const baseSalaryValue = formatToNumber($("#edit_base_salary").val());
        const rates = calculateRates(baseSalaryValue);
        $("#edit_rate_per_hour").val(
            formatToCurrency(rates.rate_per_hour.toFixed(2)),
        );
        $("#edit_rate_per_day").val(
            formatToCurrency(rates.rate_per_day.toFixed(2)),
        );
    }

    $("#payrollSettingsForm, #editSalarySettingForm, #addPositionForm").on(
        "input change",
        "input, select",
        function () {
            $(this).closest("form").attr("data-dirty", "true");
        },
    );

    $(".modal").on("hide.bs.modal", function (e) {
        const form = $(this).find("form");
        if (form.attr("data-dirty") === "true") {
            e.preventDefault();
            Swal.fire({
                title: "Unsaved Changes",
                text: "You have unsaved changes. Are you sure you want to close this window?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, discard changes",
                cancelButtonText: "No, stay here",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.removeAttr("data-dirty");
                    $(this).modal("hide");
                }
            });
        }
    });

    $("#payrollSettingsBtn").click(function () {
        $("#payrollSettingsForm").removeAttr("data-dirty");
        if (!positionsTable) {
            positionsTable = $("#positionsTable").DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "/human-resources/get-salary-settings",
                    type: "GET",
                    dataSrc: "data",
                    error: function (xhr, error, thrown) {
                        Toast.fire(
                            "Error",
                            "Could not load salary settings.",
                            "error",
                        );
                    },
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
                    { data: "position", className: "dt-left" },
                    {
                        data: "base_salary",
                        className: "dt-left",
                        render: formatToCurrency,
                    },
                    {
                        data: "rate_per_hour",
                        className: "dt-left",
                        render: formatToCurrency,
                    },
                    {
                        data: "rate_per_day",
                        className: "dt-left",
                        render: formatToCurrency,
                    },
                    { data: "department", className: "dt-left" },
                    {
                        data: "status",
                        className: "text-center",
                        width: "150px",
                    },
                    {
                        data: "id",
                        render: function (data) {
                            return `<div class="action-btns"><a href="#" class="btn icon btn-sm btn-primary edit-setting-btn bs-tooltip" data-id="${data}" title="Edit Salary"><i class="fa-solid fa-pen"></i></a></div>`;
                        },
                        className: "text-center",
                        width: "100px",
                    },
                ],
                initComplete: function () {
                    const departmentColumn = this.api().column(5);
                    const select = $("#positionDepartmentFilter");
                    select.empty().append('<option value="">All</option>');
                    departmentColumn
                        .data()
                        .unique()
                        .sort()
                        .each(function (d) {
                            if (d) {
                                select.append(
                                    $("<option></option>")
                                        .attr("value", d)
                                        .text(d),
                                );
                            }
                        });
                },
            });
            $("#positionDepartmentFilter").on("change", function () {
                const selectedDepartment = $(this).val();
                positionsTable
                    .column(5)
                    .search(
                        selectedDepartment
                            ? "^" + selectedDepartment + "$"
                            : "",
                        true,
                        false,
                    )
                    .draw();
            });
        } else {
            positionsTable.ajax.reload();
        }
        fetchAndSetDeductions();
        $("#payrollSettings").modal("show");
    });

    function fetchAndSetDeductions() {
        $.ajax({
            url: "/human-resources/get-payroll-settings",
            type: "GET",
            dataType: "json",
            success: function (data) {
                if (data) {
                    originalContributions = {
                        sss: data.sss,
                        philhealth: data.philhealth,
                        pagibig: data.pagibig,
                    };
                    $("#sss").val(formatToCurrency(data.sss));
                    $("#philhealth").val(formatToCurrency(data.philhealth));
                    $("#pagibig").val(formatToCurrency(data.pagibig));
                    $("#payrollSettingsForm").removeAttr("data-dirty");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching payroll settings:", error);
            },
        });
    }

    $("#payrollSettingsForm").on("submit", function (e) {
        e.preventDefault();
        const form = $(this);
        const currentSss = formatToNumber($("#sss").val());
        const currentPhilhealth = formatToNumber($("#philhealth").val());
        const currentPagibig = formatToNumber($("#pagibig").val());

        if (
            parseFloat(currentSss) === parseFloat(originalContributions.sss) &&
            parseFloat(currentPhilhealth) ===
                parseFloat(originalContributions.philhealth) &&
            parseFloat(currentPagibig) ===
                parseFloat(originalContributions.pagibig)
        ) {
            Toast.fire({
                icon: "info",
                title: "No Changes Detected",
                text: "The contribution values have not been changed.",
            });
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "This will update the default SSS, PhilHealth, and Pag-IBIG contributions for ALL employees. This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, update all!",
        }).then((result) => {
            if (result.isConfirmed) {
                form.find('input[inputmode="decimal"]').each(function () {
                    $(this).val(formatToNumber($(this).val()));
                });
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    url: "/human-resources/update-payroll-settings",
                    type: "POST",
                    data: form.serialize(),
                    success: function (response) {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            icon: "success",
                            title: "Success!",
                            text: response.message,
                        });
                        form.removeAttr("data-dirty");
                        fetchAndSetDeductions();
                    },
                    error: function (xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            icon: "error",
                            title: "Error",
                            text: "Failed to update settings.",
                        });
                        form.find('input[inputmode="decimal"]').each(
                            function () {
                                $(this).val(formatToCurrency($(this).val()));
                            },
                        );
                    },
                });
            }
        });
    });

    $("#positionsTable").on("click", ".edit-setting-btn", function () {
        const settingId = $(this).data("id");
        const $form = $("#editSalarySettingForm");
        $form[0].reset();
        $form.removeAttr("data-dirty");
        $form.attr(
            "action",
            `/human-resources/update-salary-setting/${settingId}`,
        );
        $("#modal-position-name").text("Loading...");
        loadSettingData(settingId);
    });

    $("#editSalarySettingModal").on("input", "#edit_base_salary", function () {
        calculateAndUpdateRates();
    });

    function loadSettingData(settingId) {
        $.ajax({
            url: `/human-resources/get-salary-setting/${settingId}`,
            type: "GET",
            success: function (data) {
                if (data) {
                    originalBaseSalary = data.base_salary;
                    $("#modal-position-name").text(data.position.name);
                    $("#edit_base_salary").val(
                        formatToCurrency(data.base_salary),
                    );
                    calculateAndUpdateRates();
                    $("#editSalarySettingForm").removeAttr("data-dirty");
                    $("#editSalarySettingModal").modal("show");
                }
            },
            error: function () {
                Toast.fire("Error", "Failed to load setting details.", "error");
            },
        });
    }

    $("#editSalarySettingForm").on("submit", function (e) {
        e.preventDefault();
        const form = $(this);
        const positionName = $("#modal-position-name").text();
        const currentBaseSalary = formatToNumber($("#edit_base_salary").val());

        if (parseFloat(currentBaseSalary) === parseFloat(originalBaseSalary)) {
            Toast.fire({
                icon: "info",
                title: "No Changes Detected",
                text: "The base salary has not been changed.",
            });
            return;
        }

        Swal.fire({
            title: "Confirm Salary Change?",
            html: `This will update the base salary for <b>all employees</b> with the "<b>${positionName}</b>" position. Are you sure?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, update it!",
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = {
                    base_salary: currentBaseSalary,
                    _token: form.find('input[name="_token"]').val(),
                    _method: form.find('input[name="_method"]').val(),
                };
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    url: form.attr("action"),
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        $("#LoadingScreen").fadeOut(200);
                        form.removeAttr("data-dirty");
                        $("#editSalarySettingModal").modal("hide");
                        Toast.fire({
                            icon: "success",
                            title: "Updated!",
                            text: response.message,
                        });
                        if (positionsTable) {
                            positionsTable.ajax.reload();
                        }
                    },
                    error: function (xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        let errorMessage = "Failed to update salary setting.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Toast.fire("Error", errorMessage, "error");
                    },
                });
            }
        });
    });

    $("#payrollSettings").on("click", "#addNewPositionBtn", function () {
        const form = $("#addPositionForm");
        form[0].reset();
        form.removeAttr("data-dirty");
        form.find(".is-invalid").removeClass("is-invalid");
        $("#addPositionModal").modal("show");
        populateDepartmentDropdown();
    });

    function populateDepartmentDropdown() {
        const $select = $("#add_department_id");
        $.ajax({
            url: "/human-resources/departments/list",
            type: "GET",
            success: function (response) {
                $select.html(
                    '<option value="" disabled selected>Select a Department</option>',
                );
                response.data.forEach(function (department) {
                    $select.append(
                        `<option value="${department.id}">${department.name}</option>`,
                    );
                });
            },
            error: function () {
                $select.html(
                    '<option value="">Could not load departments</option>',
                );
                Toast.fire("Error", "Failed to load department list.", "error");
            },
        });
    }

    $("#submitAddPosition").on("click", function (e) {
        e.preventDefault();
        let isValid = true;
        const form = $("#addPositionForm");
        form.find(".is-invalid").removeClass("is-invalid");

        form.find("input[required], select[required]").each(function () {
            if (!$(this).val()) {
                $(this).addClass("is-invalid");
                isValid = false;
            }
        });

        if (!isValid) {
            return;
        }

        const formData = form.serializeArray();
        formData.forEach(function (field) {
            if (field.name === "base_salary") {
                field.value = formatToNumber(field.value);
            }
        });
        $("#LoadingScreen").fadeIn(200);
        $.ajax({
            url: "/human-resources/store-position-and-salary",
            type: "POST",
            data: $.param(formData),
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                form.removeAttr("data-dirty");
                $("#addPositionModal").modal("hide");
                Toast.fire({
                    icon: "success",
                    title: "Created!",
                    text: response.message,
                });
                if (positionsTable) {
                    positionsTable.ajax.reload();
                }
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                let errorMessage = "Failed to create position.";
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("<br>");
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Toast.fire("Error", errorMessage, "error");
            },
        });
    });

    ///////////////////////////////////////////////////////

    const table = $("#payrollsTable").DataTable({
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
                render: function (data, type, row) {
                    if (
                        row.status ==
                        '<span class="badge bg-success">Approved</span>'
                    ) {
                        return `<input type="checkbox" class="form-check-input payroll-checkbox" value="${row.id}">`;
                    }
                    return "";
                },
                className: "text-center",
                width: "30px",
                orderable: false,
            },
            { data: "name", className: "dt-left" },
            { data: "department", className: "dt-left" },
            { data: "position", className: "dt-left" },
            {
                data: "period",
                className: "dt-left",
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
                    } else if (
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
        initComplete: function () {
            const departmentColumn = this.api().column(2);
            const departmentSelect = $("#departmentFilter");
            departmentColumn
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        departmentSelect.append(
                            $("<option></option>").attr("value", d).text(d),
                        );
                    }
                });

            const periodColumn = this.api().column(4);
            const periodSelect = $("#periodFilter");
            periodColumn
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        periodSelect.append(
                            $("<option></option>").attr("value", d).text(d),
                        );
                    }
                });
        },
    });

    $("#btn-refresh-payrolls").on("click", function () {
        $("#selectAllCheckbox").prop("checked", false);
        $(".payroll-checkbox").prop("checked", false);
        updateBatchReleaseButton();
        table.ajax.reload(null, false);
    });

    $("#departmentFilter").on("change", function () {
        const selectedDepartment = $(this).val();
        table
            .column(2)
            .search(
                selectedDepartment ? "^" + selectedDepartment + "$" : "",
                true,
                false,
            )
            .draw();
    });

    $("#periodFilter").on("change", function () {
        const selectedPeriod = $(this).val();
        table
            .column(4)
            .search(
                selectedPeriod ? "^" + selectedPeriod + "$" : "",
                true,
                false,
            )
            .draw();
    });

    $("#selectAllCheckbox").on("click", function () {
        const isChecked = $(this).is(":checked");
        $(".payroll-checkbox").prop("checked", isChecked);
        updateBatchReleaseButton();
    });

    $(document).on("change", ".payroll-checkbox", function () {
        const totalCheckboxes = $(".payroll-checkbox").length;
        const checkedCheckboxes = $(".payroll-checkbox:checked").length;
        $("#selectAllCheckbox").prop(
            "checked",
            totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes,
        );
        updateBatchReleaseButton();
    });

    function updateBatchReleaseButton() {
        const checkedCount = $(".payroll-checkbox:checked").length;
        if (checkedCount > 0) {
            $("#btn-batch-release").show();
        } else {
            $("#btn-batch-release").hide();
        }
    }

    $("#btn-batch-release").on("click", function (e) {
        e.preventDefault();
        const selectedIds = $(".payroll-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if (selectedIds.length === 0) {
            Toast.fire(
                "Error",
                "Please select at least one payroll to release.",
                "error",
            );
            return;
        }

        Swal.fire({
            title: "Batch Release Payroll?",
            html: `You are about to release <b>${selectedIds.length}</b> payroll(s). This action cannot be undone.`,
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
                        "content",
                    ),
                },
                url: `/human-resources/payroll/batch-release`,
                type: "PUT",
                data: {
                    ids: selectedIds,
                },
                success: function (response) {
                    if (response.success) {
                        $("#LoadingScreen").fadeOut(200);
                        $("#selectAllCheckbox").prop("checked", false);
                        $(".payroll-checkbox").prop("checked", false);
                        updateBatchReleaseButton();
                        reloadTable("payrollsTable");
                        Toast.fire({
                            text: response.message,
                            icon: "success",
                        });
                    } else {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire("Error", response.message, "error");
                    }
                },
                error: function (xhr) {
                    $("#LoadingScreen").fadeOut(200);
                    if (xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(
                            xhr.responseJSON.errors,
                        )
                            .flat()
                            .join("\n");
                        Toast.fire("Validation Error", errorMessages, "error");
                    } else {
                        Toast.fire(
                            "Error",
                            xhr.responseJSON?.message || "Something went wrong",
                            "error",
                        );
                    }
                },
            });
        });
    });

    $(document).on("click", ".btn-view", function () {
        const payroll_id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(
            `/human-resources/payroll/view/${payroll_id}`,
            function (response) {
                const isAcknowledged = response.data.status_id == 38;

                buildPayslipModal(response.data);

                const $modal = $("#viewPayroll");
                const $dialog = $modal.find(".modal-dialog");
                const $body = $modal.find(".modal-body");

                $dialog.removeClass("modal-fullscreen");

                if (isAcknowledged && response.data.proof_of_payment) {
                    $dialog.addClass("modal-fullscreen");

                    if ($body.children().length > 0) {
                        $body.wrapInner(
                            '<div class="col-md-7 h-100" style="overflow-y: auto;"></div>',
                        );
                        const $leftCol = $body.children().first();

                        const $rightCol = $(`
                                <div class="col-md-5 proof-payment-container border-start ps-4 h-100" style="overflow-y: auto; background-color: #f8f9fa;">
                                    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                                        <h5 class="fw-bold mb-0">Proof of Payment</h5>
                                        <a href="${response.data.proof_of_payment}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-expand"></i>
                                        </a>
                                    </div>
                                    <div class="text-center rounded d-flex flex-column align-items-center justify-content-start">
                                        <img src="${response.data.proof_of_payment}" class="img-fluid rounded shadow-sm" alt="Proof of Payment" style="width: 100%; object-fit: contain;">
                                    </div>
                                </div>
                            `);

                        const $row = $('<div class="row h-100"></div>');

                        $leftCol.appendTo($row);
                        $rightCol.appendTo($row);

                        $body.append($row);
                    }
                }

                $("#LoadingScreen").fadeOut(200);
            },
        ).fail(function () {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire("Error", "Failed to load payslip.", "error");
        });
    });

    $(document).on("click", ".btn-print-payslip", function () {
        const payroll_id = $(this).data("id");
        $.get(
            `/human-resources/payroll/view/${payroll_id}`,
            function (response) {
                printPayslip(response.data);
            },
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
                        "content",
                    ),
                },
                url: `/human-resources/payroll/release/${id}`,
                type: "PUT",
                data: null,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $("#LoadingScreen").fadeOut(200);
                        reloadTable("payrollsTable");
                        Toast.fire({
                            text: response.message,
                            icon: "success",
                        });
                    } else {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire("Error", response.message, "error");
                    }
                },
                error: function (xhr) {
                    $("#LoadingScreen").fadeOut(200);
                    if (xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(
                            xhr.responseJSON.errors,
                        )
                            .flat()
                            .join("\n");
                        Toast.fire("Validation Error", errorMessages, "error");
                    } else {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire(
                            "Error",
                            xhr.responseJSON?.message || "Something went wrong",
                            "error",
                        );
                    }
                },
            });
        });
    });
});
