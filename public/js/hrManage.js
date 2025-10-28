$(function() {
    var mode = "";
    let defaultSss, defaultPhilhealth, defaultPagibig;

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
                        $("#base_salary").val(formatToCurrency(data.base_salary));
                    } else {
                        $("#base_salary").val("");
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
                                location.reload();
                            } else if (isEditMode) {
                                window.location.href = "{{ route('hr.employees') }}";
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
