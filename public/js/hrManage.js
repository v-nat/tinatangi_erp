$(function() {

    var mode = "";

    $("#birth_date").on("change", function () {
        let birthday = new Date($(this).val());
        if (!isNaN(birthday.getTime())) {
            let ageDifMs = Date.now() - birthday.getTime();
            let ageDate = new Date(ageDifMs);
            $("#age").val(Math.abs(ageDate.getUTCFullYear() - 1970));
        } else {
             $("#age").val("");
        }
    });

    $('input[type="tel"]').not('#postal_code').on("input", function () {
         $(this).val(
             $(this).val().replace(/[^\d+]/g, "")
         );
     });

    $("#postal_code").on("input", function () {
        let value = this.value.replace(/[^\d]/g, '');
        if (value.length > 4) {
            value = value.slice(0, 4);
        }
        this.value = value;
    });

    mode = $("#insert-btn-employee").data("mode");
    if (mode) {
         setButtonMode(mode);
    }

     function setButtonMode(mode) {
        const button = document.getElementById("insert-btn-employee");
        if (!button) return;
        if (mode === "add") {
            $("#insert-btn-employee").removeAttr("hidden");
            $("#edit-btn-employee").attr("hidden", true);
            $("#cancel").attr("hidden", true);
            $("#reset").removeAttr("hidden");
            $("#email").removeAttr("readonly");
        } else if (mode === "edit") {
            $("#insert-btn-employee").attr("hidden", true);
            $("#reset").attr("hidden", true);
            $("#edit-btn-employee").removeAttr("hidden");
            $("#cancel").removeAttr("hidden");
            $("#email").attr("readonly", "readonly");
        }
    }

    const departmentSelect = document.getElementById("department");
    const positionSelect = document.getElementById("position");
    const supervisorSelect = document.getElementById("supervisor");
    const baseSalary = [20800.0, 16640.0, 15600.0, 14560.0, 13520.0];

    if (mode === "add") {
         if(departmentSelect) departmentSelect.addEventListener("change", updatePositions);
         if(positionSelect) positionSelect.addEventListener("change", updateSupervisors);
    } else if (mode === "edit") {
        const supervisorData = $("#supervisor").data("value");
        const positionData = $("#position").data("value");

         if (supervisorData && supervisorSelect) {
            const value = supervisorData.split("|");
             supervisorSelect.innerHTML = '';
             const option = document.createElement("option");
             option.value = value[1];
             option.textContent = value[0];
             supervisorSelect.appendChild(option).selected = true;
         }

         if (positionData && positionSelect) {
             const [position, positionId, levelValue] = positionData.split("|");
             positionSelect.innerHTML = '';
             const opt = document.createElement("option");
             opt.value = positionId;
             // option.dataset.level = levelValue; // Original Bug reference
             opt.textContent = position;
             positionSelect.appendChild(opt).selected = true;
             $("#level").val(levelValue ? levelValue.toUpperCase() : '');
         }

        if(departmentSelect) departmentSelect.addEventListener("change", updatePositions);
        if(positionSelect) positionSelect.addEventListener("change", updateSupervisors);
    }
    function updatePositions() {
        $("#level").val("");
        $("#base_salary").val("");
        const department = departmentSelect.value;
        if (department) {
            fetch(
                `/human-resources/positions-by-department?department=${encodeURIComponent(
                    department
                )}`
            )
                .then((res) => res.json())
                .then((data) => {
                    const positionSelect = document.getElementById("position");
                    positionSelect.innerHTML =
                        '<option value="" disabled selected>Choose...</option>';

                    data.forEach((p) => {
                        const option = document.createElement("option");
                        option.dataset.level = p.level;
                        option.value = p.id;
                        option.textContent = p.name;
                        positionSelect.appendChild(option);
                    });
                     if (supervisorSelect) supervisorSelect.innerHTML = '<option value="" disabled selected>Choose...</option>';
                     if (positionSelect.value) updateSupervisors();
                })
                .catch(error => console.error('Error fetching positions:', error));
        } else {
             if (positionSelect) positionSelect.innerHTML = '<option value="" disabled selected>Choose...</option>';
             if (supervisorSelect) supervisorSelect.innerHTML = '<option value="" disabled selected>Choose...</option>';
             $("#level").val("");
             $("#base_salary").val("");
         }
    }

    function updateSupervisors() {
        const department = departmentSelect.value;
        const position = positionSelect.value;
        const level = $("#position option:selected").data("level");
        $("#level").val(level ? level.toUpperCase() : '');
        updateSalary();
        if (position && department) {
            fetch(
                `/human-resources/supervisors-by-department-and-position?department=${encodeURIComponent(
                    department
                )}&position=${encodeURIComponent(position)}`
            )
                .then((response) => response.json())
                .then((data) => {
                    supervisorSelect.innerHTML =
                        '<option value="" disabled selected>Choose...</option>';
                    data.forEach((s) => {
                        const option = document.createElement("option");
                        option.value = s.id;
                        option.textContent = (s.user ? s.user.first_name + " " + s.user.last_name : s.first_name + " " + s.last_name);
                        supervisorSelect.appendChild(option);
                    });
                })
                 .catch(error => console.error('Error fetching supervisors:', error));
        } else {
            supervisorSelect.innerHTML =
                '<option value="" disabled selected>Choose...</option>';
        }
    }
     function updateSalary() {
         const position = $("#position option:selected").val();
         let salaryValue = "";
         const salaryMap = {
            "1": baseSalary[0], "2": baseSalary[0], "3": baseSalary[1],
            "4": baseSalary[0], "5": baseSalary[0], "6": baseSalary[0],
            "7": baseSalary[4], "8": baseSalary[0], "9": baseSalary[1],
            "10": baseSalary[0], "11": baseSalary[2], "12": baseSalary[4],
            "13": baseSalary[0], "14": baseSalary[1], "15": baseSalary[4],
            "16": baseSalary[3], "17": baseSalary[4]
        };
         if (position && salaryMap[position] !== undefined) {
            salaryValue = salaryMap[position].toFixed(2);
        }
         $("#base_salary").val(salaryValue);
    }

    $("#reset").click(function (e) {
        const $form = $("#employeeForm");
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").hide().text('');

        setTimeout(function() {
             $("#sss").val(600.00);
             $("#philhealth").val(450.00);
             $("#pagibig").val(100.00);
             $('#schedule_color').val('#3788D8');
             $('#age').val('');
              // Reset schedule checkboxes
             $('#employeeForm .day-checkboxes .form-check-input').prop('checked', false);
             $('#department').trigger('change');
        }, 0);
    });

    $("#cancel").click(function (e) {
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

    $("#insert-btn-employee, #edit-btn-employee").click(function (e) {
        e.preventDefault();
        let isValid = true;
        const $form = $("#employeeForm");
        const isEditMode = $(this).attr('id') === 'edit-btn-employee';
        const submitUrl = isEditMode ? `/human-resources/update-employee/${$("#email").data("value")}` : "/human-resources/store-employee";
        const submitMethod = "POST";

        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").hide().text('');

        $form.find("input[required], select[required]").each(function () {
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
            let formData = new FormData($form[0]);
            if (isEditMode) {
                formData.append("_method", "PUT");
            }

             const submitAction = () => {
                 $("#LoadingScreen").fadeIn(200);
                 $.ajax({
                    url: submitUrl,
                    type: submitMethod,
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            text: response.message,
                            icon: "success",
                         }).then(() => {
                            if (typeof Toast !== 'undefined') {
                                location.reload();
                            } else if(isEditMode) {
                                window.location.href = "{{ route('hr.employees') }}";
                            } else {
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
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
                                 }
                                 else if (inputElement.length) {
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
                             // Use original Toast
                             if (typeof Toast !== 'undefined') {
                                 Toast.fire(
                                    "Error",
                                    xhr.responseJSON?.message || "An unexpected error occurred.",
                                    "error"
                                );
                             } else {
                                Swal.fire(
                                    "Error",
                                    xhr.responseJSON?.message || "An unexpected error occurred.",
                                    "error"
                                );
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
             // Use original Toast
             if (typeof Toast !== 'undefined') {
                 Toast.fire('Validation Error', 'Please check the required fields and ensure schedule details are correct.', 'warning');
             } else {
                  Swal.fire('Validation Error', 'Please check the required fields and ensure schedule details are correct.', 'warning');
             }
        }
    });

});

