$(function() {
    var mode = "";
    let defaultSss, defaultPhilhealth, defaultPagibig;

    const sssTable = [
        { min: 0,        max: 5249.99,  deduction: 250.00 },
        { min: 5250,     max: 5749.99,  deduction: 275.00 },
        { min: 5750,     max: 6249.99,  deduction: 300.00 },
        { min: 6250,     max: 6749.99,  deduction: 325.00 },
        { min: 6750,     max: 7249.99,  deduction: 350.00 },
        { min: 7250,     max: 7749.99,  deduction: 375.00 },
        { min: 7750,     max: 8249.99,  deduction: 400.00 },
        { min: 8250,     max: 8749.99,  deduction: 425.00 },
        { min: 8750,     max: 9249.99,  deduction: 450.00 },
        { min: 9250,     max: 9749.99,  deduction: 475.00 },
        { min: 9750,     max: 10249.99, deduction: 500.00 },
        { min: 10250,    max: 10749.99, deduction: 525.00 },
        { min: 10750,    max: 11249.99, deduction: 550.00 },
        { min: 11250,    max: 11749.99, deduction: 575.00 },
        { min: 11750,    max: 12249.99, deduction: 600.00 },
        { min: 12250,    max: 12749.99, deduction: 625.00 },
        { min: 12750,    max: 13249.99, deduction: 650.00 },
        { min: 13250,    max: 13749.99, deduction: 675.00 },
        { min: 13750,    max: 14249.99, deduction: 700.00 },
        { min: 14250,    max: 14749.99, deduction: 725.00 },
        { min: 14750,    max: 15249.99, deduction: 750.00 },
        { min: 15250,    max: 15749.99, deduction: 775.00 },
        { min: 15750,    max: 16249.99, deduction: 800.00 },
        { min: 16250,    max: 16749.99, deduction: 825.00 },
        { min: 16750,    max: 17249.99, deduction: 850.00 },
        { min: 17250,    max: 17749.99, deduction: 875.00 },
        { min: 17750,    max: 18249.99, deduction: 900.00 },
        { min: 18250,    max: 18749.99, deduction: 925.00 },
        { min: 18750,    max: 19249.99, deduction: 950.00 },
        { min: 19250,    max: 19749.99, deduction: 975.00 },
        { min: 19750,    max: 20249.99, deduction: 1000.00 },

        { min: 20250,    max: 20749.99, deduction: 1025.00 },
        { min: 20750,    max: 21249.99, deduction: 1050.00 },
        { min: 21250,    max: 21749.99, deduction: 1075.00 },
        { min: 21750,    max: 22249.99, deduction: 1100.00 },
        { min: 22250,    max: 22749.99, deduction: 1125.00 },
        { min: 22750,    max: 23249.99, deduction: 1150.00 },
        { min: 23250,    max: 23749.99, deduction: 1175.00 },
        { min: 23750,    max: 24249.99, deduction: 1200.00 },
        { min: 24250,    max: 24749.99, deduction: 1225.00 },
        { min: 24750,    max: 25249.99, deduction: 1250.00 },
        { min: 25250,    max: 25749.99, deduction: 1275.00 },
        { min: 25750,    max: 26249.99, deduction: 1300.00 },
        { min: 26250,    max: 26749.99, deduction: 1325.00 },
        { min: 26750,    max: 27249.99, deduction: 1350.00 },
        { min: 27250,    max: 27749.99, deduction: 1375.00 },
        { min: 27750,    max: 28249.99, deduction: 1400.00 },
        { min: 28250,    max: 28749.99, deduction: 1425.00 },
        { min: 28750,    max: 29249.99, deduction: 1450.00 },
        { min: 29250,    max: 29749.99, deduction: 1475.00 },
        { min: 29750,    max: 30249.99, deduction: 1500.00 },
        { min: 30250,    max: 30749.99, deduction: 1525.00 },
        { min: 30750,    max: 31249.99, deduction: 1550.00 },
        { min: 31250,    max: 31749.99, deduction: 1575.00 },
        { min: 31750,    max: 32249.99, deduction: 1600.00 },
        { min: 32250,    max: 32749.99, deduction: 1625.00 },
        { min: 32750,    max: 33249.99, deduction: 1650.00 },
        { min: 33250,    max: 33749.99, deduction: 1675.00 },
        { min: 33750,    max: 34249.99, deduction: 1700.00 },
        { min: 34250,    max: 34749.99, deduction: 1725.00 },
        { min: 34750,    max: Infinity, deduction: 1750.00 }
    ];

    function calculateSSSDeduction(salary) {
        if (!salary && salary !== 0) return 0;

        const bracket = sssTable.find(b => salary >= b.min && salary <= b.max);

        return bracket ? bracket.deduction : 0;
    }

    const currencyFields = ['#base_salary', '#sss', '#philhealth', '#pagibig'];

    function formatToCurrency(value) {
        if (value === null || value === undefined || value === '') return '';
        const number = Number(String(value).replace(/[₱,]/g, ''));
        if (isNaN(number)) return '';
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(number);
    }

    function formatToNumber(value) {
        if (typeof value !== 'string') return value;
        return value.replace(/[₱,]/g, '').trim();
    }

    $(currencyFields.join(', ')).on({
        focus: function() {
            $(this).val(formatToNumber($(this).val()));
        },
        blur: function() {
            $(this).val(formatToCurrency($(this).val()));

            if ($(this).is('#base_salary')) {
                const salary = parseFloat(formatToNumber($(this).val()));
                if (!isNaN(salary)) {
                    const newSss = calculateSSSDeduction(salary);
                    $('#sss').val(formatToCurrency(newSss));
                }
            }
        }
    });

    function fetchAndSetDeductions() {
        $.ajax({
            url: '/human-resources/get-payroll-settings',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data) {
                    defaultSss = parseFloat(data.sss).toFixed(2);
                    defaultPhilhealth = parseFloat(data.philhealth).toFixed(2);
                    defaultPagibig = parseFloat(data.pagibig).toFixed(2);

                    if (mode === 'add') {
                        $("#sss").val(formatToCurrency(defaultSss));
                        $("#philhealth").val(formatToCurrency(defaultPhilhealth));
                        $("#pagibig").val(formatToCurrency(defaultPagibig));
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching payroll settings:', error);
            }
        });
    }

    $("#birth_date").on("change", function() {
        let birthday = new Date($(this).val());
        if (!isNaN(birthday.getTime())) {
            let ageDifMs = Date.now() - birthday.getTime();
            let ageDate = new Date(ageDifMs);
            $("#age").val(Math.abs(ageDate.getUTCFullYear() - 1970));
        } else {
            $("#age").val("");
        }
    });

    $('input[type="tel"]').not('#postal_code').on("input", function() {
        $(this).val($(this).val().replace(/[^\d+]/g, ""));
    });

    $("#postal_code").on("input", function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        if (value.length > 4) value = value.slice(0, 4);
        $(this).val(value);
    });

    mode = $("#insert-btn-employee").data("mode");
    if (mode) {
        setButtonMode(mode);
    }
    fetchAndSetDeductions();

    $.each(currencyFields, function(index, selector) {
        const $field = $(selector);
        if ($field.val()) {
            $field.val(formatToCurrency($field.val()));
        }
    });

    function setButtonMode(mode) {
        const $button = $("#insert-btn-employee");
        if (!$button.length) return;

        if (mode === "add") {
            $button.removeAttr("hidden");
            $("#edit-btn-employee").attr("hidden", true);
            $("#cancel").attr("hidden", true);
            $("#reset").removeAttr("hidden");
            $("#email").removeAttr("readonly");
        } else if (mode === "edit") {
            $button.attr("hidden", true);
            $("#reset").attr("hidden", true);
            $("#edit-btn-employee").removeAttr("hidden");
            $("#cancel").removeAttr("hidden");
            $("#email").attr("readonly", "readonly");
        }
    }

    const $departmentSelect = $("#department");
    const $positionSelect = $("#position");
    const $supervisorSelect = $("#supervisor");

    if ($departmentSelect.length) $departmentSelect.on("change", updatePositions);
    if ($positionSelect.length) $positionSelect.on("change", updateSupervisors);

    if (mode === "edit") {
        const supervisorData = $supervisorSelect.data("value");
        const positionData = $positionSelect.data("value");

        if (supervisorData && $supervisorSelect.length) {
            const value = supervisorData.split("|");
            $supervisorSelect.html('').append(new Option(value[0], value[1], true, true));
        }

        if (positionData && $positionSelect.length) {
            const [position, positionId, levelValue] = positionData.split("|");
            $positionSelect.html('').append(new Option(position, positionId, true, true));
            $("#level").val(levelValue ? levelValue.toUpperCase() : '');
        }
    }

    function updatePositions() {
        $("#level").val("");
        $("#base_salary").val("");
        const department = $departmentSelect.val();

        if (department) {
            $.ajax({
                url: `/human-resources/positions-by-department?department=${encodeURIComponent(department)}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $positionSelect.html('<option value="" disabled selected>Choose...</option>');
                    $.each(data, function(i, p) {
                        const $option = $('<option>', {
                            value: p.id,
                            text: p.name
                        }).data('level', p.level);
                        $positionSelect.append($option);
                    });
                    if ($supervisorSelect.length) $supervisorSelect.html('<option value="" disabled selected>Choose...</option>');
                    if ($positionSelect.val()) updateSupervisors();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching positions:', error);
                }
            });
        } else {
            if ($positionSelect.length) $positionSelect.html('<option value="" disabled selected>Choose...</option>');
            if ($supervisorSelect.length) $supervisorSelect.html('<option value="" disabled selected>Choose...</option>');
            $("#level").val("");
            $("#base_salary").val("");
        }
    }

    function updateSupervisors() {
        const department = $departmentSelect.val();
        const position = $positionSelect.val();
        const level = $("#position option:selected").data("level");
        $("#level").val(level ? level.toUpperCase() : '');
        updateSalary();

        if (position && department) {
            $.ajax({
                url: `/human-resources/supervisors-by-department-and-position?department=${encodeURIComponent(department)}&position=${encodeURIComponent(position)}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $supervisorSelect.html('<option value="" disabled selected>Choose...</option>');
                    $.each(data, function(i, s) {
                        const name = (s.user ? `${s.user.first_name} ${s.user.last_name}` : `${s.first_name} ${s.last_name}`);
                        $supervisorSelect.append($('<option>', {
                            value: s.id,
                            text: name
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching supervisors:', error);
                }
            });
        } else {
            $supervisorSelect.html('<option value="" disabled selected>Choose...</option>');
        }
    }

    function updateSalary() {
        const positionId = $positionSelect.val();
        if (positionId) {
            $.ajax({
                url: `/human-resources/get-salary-by-position?position_id=${encodeURIComponent(positionId)}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && data.base_salary) {
                        const rawSalary = parseFloat(data.base_salary);

                        // Set Salary Field
                        $("#base_salary").val(formatToCurrency(rawSalary));

                        // --- AUTO-CALCULATE SSS ---
                        if (!isNaN(rawSalary)) {
                            const newSss = calculateSSSDeduction(rawSalary);
                            $('#sss').val(formatToCurrency(newSss));
                        }
                    } else {
                        $("#base_salary").val("");
                        // Reset SSS to default if salary is cleared
                        if(defaultSss) $('#sss').val(formatToCurrency(defaultSss));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching salary:', error);
                    $("#base_salary").val("");
                }
            });
        } else {
            $("#base_salary").val("");
        }
    }

    $("#reset").click(function(e) {
        const $form = $("#employeeForm");
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").hide().text('');

        setTimeout(function() {
            $("#sss").val(formatToCurrency(defaultSss || '0.00'));
            $("#philhealth").val(formatToCurrency(defaultPhilhealth || '0.00'));
            $("#pagibig").val(formatToCurrency(defaultPagibig || '0.00'));
            $('#schedule_color').val('#3788D8');
            $('#age').val('');
            $('#employeeForm .day-checkboxes .form-check-input').prop('checked', false);
            $('#department').trigger('change');
        }, 0);
    });

    $("#cancel").click(function(e) {
        e.preventDefault();
        window.history.back();
    });

    var $dayCheckboxes = $('#employeeForm .day-checkboxes .form-check-input');

    function clearScheduleValidationErrors() {
        $('#employeeForm #schedule_title, #employeeForm [name="days_of_week[]"], #employeeForm #schedule_time_in, #employeeForm #schedule_time_out, #employeeForm #schedule_color, #employeeForm #schedule_description')
            .removeClass('is-invalid');
        $('#employeeForm .invalid-feedback[id$="_error"]').hide().text('');
        $('#days_of_week_error').hide().text('Please select exactly 6 days.');
    }

    function displayScheduleValidationErrors(errors) {
        let daysOfWeekErrorHandled = false;
        $.each(errors, function(key, value) {
            const errorElement = $(`#${key}_error`);
            const inputElement = $(`#employeeForm [name='${key}'], #employeeForm [name='${key}[]']`);
            if (inputElement.length) inputElement.addClass('is-invalid');
            if (errorElement.length) {
                errorElement.show().text(value[0]);
                if (key === 'days_of_week') {
                    daysOfWeekErrorHandled = true;
                    $('#employeeForm .day-checkboxes .form-check-input').addClass('is-invalid');
                }
            } else if (key.startsWith('days_of_week.')) {
                $('#days_of_week_error').show().text(value[0]);
                $('#employeeForm .day-checkboxes .form-check-input').addClass('is-invalid');
                daysOfWeekErrorHandled = true;
            } else if (inputElement.length) {
                inputElement.siblings('.invalid-feedback').show().text(value[0]);
            }
        });
        if (errors.days_of_week && !daysOfWeekErrorHandled && Array.isArray(errors.days_of_week)) {
            $('#days_of_week_error').show().text(errors.days_of_week[0]);
            $('#employeeForm .day-checkboxes .form-check-input').addClass('is-invalid');
        }
    }

    if ($dayCheckboxes.length > 0) {
        $dayCheckboxes.on('change', function() {
            let checkedCount = $dayCheckboxes.filter(':checked').length;
            if (checkedCount > 6) {
                $(this).prop('checked', false);
                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Selection Limit',
                        text: 'You can only select up to 6 days (one day off).',
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Limit',
                        text: 'You can only select up to 6 days (one day off).',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
            if (checkedCount === 6) {
                $dayCheckboxes.removeClass('is-invalid');
                $('#days_of_week_error').hide().text('Please select exactly 6 days.');
            }
        });
    }

    $("#insert-btn-employee, #edit-btn-employee").click(function(e) {
        e.preventDefault();
        let isValid = true;
        const $form = $("#employeeForm");
        const isEditMode = $(this).attr('id') === 'edit-btn-employee';
        const submitUrl = isEditMode ? `/human-resources/update-employee/${$("#email").data("value")}` : "/human-resources/store-employee";
        const submitMethod = "POST";
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").hide().text('');
        $form.find("input[required], select[required]").each(function() {
            const $field = $(this);
            const value = $field.val();
            const labelText = $field.prev('label').text() || $field.attr('placeholder') || $field.attr('name');
            const feedbackDiv = $field.siblings(".invalid-feedback");
            if (!value || (typeof value === 'string' && !value.trim())) {
                $field.addClass("is-invalid");
                if (feedbackDiv.length) {
                    feedbackDiv.show().text(labelText + ' is required.');
                } else {
                    $field.after('<div class="invalid-feedback" style="display: block;">' + labelText + ' is required.</div>');
                }
                isValid = false;
            }
        });
        if ($dayCheckboxes.length > 0) {
            let checkedDays = $dayCheckboxes.filter(':checked').length;
            let scheduleRequired = $('#schedule_time_in').val() || $('#schedule_time_out').val();
            if (scheduleRequired && checkedDays !== 6) {
                $dayCheckboxes.closest('.day-checkboxes').find('.form-check-input').addClass('is-invalid');
                $('#days_of_week_error').show().text('Please select exactly 6 days for the schedule.');
                isValid = false;
            }
        }
        const timeInVal = $('#schedule_time_in').val();
        const timeOutVal = $('#schedule_time_out').val();
        let scheduleSeemsRequired = $dayCheckboxes.filter(':checked').length > 0 || timeInVal || timeOutVal;
        if (scheduleSeemsRequired) {
            if (timeInVal && !timeOutVal) {
                $('#schedule_time_out').addClass('is-invalid');
                $('#time_out_error').show().text('End time is required.');
                isValid = false;
            } else if (!timeInVal && timeOutVal) {
                $('#schedule_time_in').addClass('is-invalid');
                $('#time_in_error').show().text('Start time is required.');
                isValid = false;
            } else if (timeInVal && timeOutVal && timeOutVal <= timeInVal) {
                $('#schedule_time_out').addClass('is-invalid');
                $('#time_out_error').show().text('End time must be after start time.');
                isValid = false;
            }
        }
        const postalCodeField = $("#postal_code");
        if (postalCodeField.val() && !/^\d{4}$/.test(postalCodeField.val())) {
            postalCodeField.addClass("is-invalid").siblings(".invalid-feedback").show().text('Postal Code must be exactly 4 digits.');
            isValid = false;
        }
        const phoneField = $("#phone_number");
        if (phoneField.val() && !/^(09|\+639)\d{9}$/.test(phoneField.val())) {
            phoneField.addClass("is-invalid").siblings(".invalid-feedback").show().text('Phone Number must be a valid 11-digit PH number (09...).');
            isValid = false;
        }

        if (isValid) {
            const originalValues = {};
            $.each(currencyFields, function(index, selector) {
                const field = $(selector);
                originalValues[selector] = field.val();
                field.val(formatToNumber(field.val()));
            });

            let formData = new FormData($form[0]);
            if (isEditMode) {
                formData.append("_method", "PUT");
            }

            $.each(currencyFields, function(index, selector) {
                $(selector).val(originalValues[selector]);
            });

            const submitAction = () => {
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    url: submitUrl,
                    type: submitMethod,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            text: response.message,
                            icon: "success",
                        }).then(() => {
                            if (typeof Toast !== 'undefined') {
                                window.location.href = "/human-resources/employees";
                            } else {
                                location.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                let inputElement = $(`#employeeForm [name='${key}'], #employeeForm [name='${key}[]']`);
                                if (!inputElement.length) inputElement = $(`#employeeForm #${key}`);
                                let errorElement = $(`#${key}_error`);
                                if (key === 'days_of_week' || key.startsWith('days_of_week.') || key === 'time_in' || key === 'time_out' || key.startsWith('schedule_')) {
                                    let scheduleErrors = {};
                                    scheduleErrors[key] = value;
                                    displayScheduleValidationErrors(scheduleErrors);
                                } else if (inputElement.length) {
                                    inputElement.addClass('is-invalid');
                                    if (errorElement.length) {
                                        errorElement.show().text(value[0]);
                                    } else {
                                        inputElement.siblings('.invalid-feedback').show().text(value[0]);
                                    }
                                } else {
                                    console.warn("Could not find element for validation error:", key);
                                }
                            });
                            if (typeof Toast !== 'undefined') {
                                Toast.fire("Validation Error", "Please check the highlighted fields.", "error");
                            } else {
                                Swal.fire("Validation Error", "Please check the highlighted fields.", "error");
                            }
                        } else {
                            if (typeof Toast !== 'undefined') {
                                Toast.fire("Error", xhr.responseJSON?.message || "An unexpected error occurred.", "error");
                            } else {
                                Swal.fire("Error", xhr.responseJSON?.message || "An unexpected error occurred.", "error");
                            }
                        }
                    },
                });
            };

            if (isEditMode) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You are about to update this employee's information.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, update it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitAction();
                    }
                });
            } else {
                submitAction();
            }

        } else {
            if (typeof Toast !== 'undefined') {
                Toast.fire('Validation Error', 'Please check the required fields and ensure schedule details are correct.', 'warning');
            } else {
                Swal.fire('Validation Error', 'Please check the required fields and ensure schedule details are correct.', 'warning');
            }
        }
    });
});
