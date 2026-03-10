const csrfToken = $('meta[name="csrf-token"]').attr("content") || "";

const requestJson = (url, settings = {}) =>
    new Promise((resolve, reject) => {
        $.ajax({
            url,
            method: settings.method || "GET",
            dataType: "json",
            data: settings.body ?? settings.data ?? null,
            processData: settings.processData ?? (settings.body ? false : true),
            contentType: settings.contentType,
            headers: {
                Accept: "application/json",
                ...(settings.headers || {}),
            },
            success: resolve,
            error: (jqXHR) => {
                const error = new Error("Request failed");
                error.status = jqXHR.status;
                error.payload = jqXHR.responseJSON || {};
                reject(error);
            },
        });
    });

const showToast = (title, text, icon = "info") => {
    if (typeof Toast !== "undefined") {
        Toast.fire({ icon, title, text });
    } else if (typeof Swal !== "undefined") {
        Swal.fire({ icon, title, text });
    } else {
        console.info(`${icon.toUpperCase()}: ${title} - ${text}`);
    }
};

const parseTimeToMinutes = (time) => {
    if (!time) return null;
    const [hours, minutes] = time.split(":").map(Number);
    if (Number.isNaN(hours) || Number.isNaN(minutes)) return null;
    return hours * 60 + minutes;
};

const confirmAction = ({
    title = "Are you sure?",
    text = "This action cannot be undone.",
    icon = "warning",
    confirmText = "Yes",
    cancelText = "Cancel",
} = {}) =>
    new Promise((resolve) => {
        if (typeof Swal !== "undefined") {
            Swal.fire({
                title,
                text,
                icon,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                focusCancel: true,
            }).then((result) => resolve(Boolean(result.isConfirmed)));
        } else {
            resolve(window.confirm(text));
        }
    });

$(function () {
    const $employeeList = $("#scheduleEmployeeList");
    const $employeeSearch = $("#scheduleEmployeeSearch");
    const $form = $("#scheduleManagementForm");
    const $employeeLabel = $("#selectedEmployeeLabel");
    const $employeeIdInput = $("#scheduleEmployeeId");
    const $scheduleIdInput = $("#scheduleId");
    const $statusBadge = $("#scheduleStatusBadge");
    const $calendarBadge = $("#calendarViewBadge");
    const $saveBtn = $("#saveScheduleBtn");
    const $resetBtn = $("#resetScheduleBtn");
    const $deleteBtn = $("#deleteScheduleBtn");
    const $dayCheckboxes = $form.find(".day-checkboxes .form-check-input");
    const $daysError = $("#days_of_week_error");
    const $timeIn = $("#schedule_time_in");
    const $timeOut = $("#schedule_time_out");
    const $timeInError = $("#time_in_error");
    const $timeOutError = $("#time_out_error");
    const calendarEl = document.getElementById("schedulePreviewCalendar");

    let currentEmployeeId = null;
    let currentSchedule = null;
    let calendarInstance = null;
    let searchTimeout = null;

    const defaultStatusClasses = $statusBadge.attr("class");
    const defaultCalendarClasses = $calendarBadge.attr("class");

    const setStatusBadge = (text, classes = defaultStatusClasses) => {
        $statusBadge.attr("class", classes).text(text);
    };

    const setCalendarBadge = (text, classes = defaultCalendarClasses) => {
        $calendarBadge.attr("class", classes).text(text);
    };

    const setFormDisabled = (disabled) => {
        $saveBtn.prop("disabled", disabled);
        $resetBtn.prop("disabled", disabled);
        $deleteBtn.prop("disabled", disabled || !currentSchedule);
        $form
            .find(":input")
            .not("#scheduleEmployeeId, #scheduleId")
            .prop("disabled", disabled);
    };

    const clearFormErrors = () => {
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find(".invalid-feedback").each(function () {
            $(this).addClass("d-none").text("");
        });
        $daysError
            .addClass("d-none")
            .text("Please select exactly six working days.");
    };

    const clearForm = () => {
        const preservedEmployeeId = $employeeIdInput.val();
        $form[0].reset();
        $employeeIdInput.val(preservedEmployeeId);
        $scheduleIdInput.val("");
        $("#schedule_color").val("#3788D8");
        $dayCheckboxes.prop("checked", false);
        clearFormErrors();
        currentSchedule = null;
        $deleteBtn.prop("disabled", true);
        setCalendarBadge(
            "No schedule loaded",
            "badge bg-primary-subtle text-primary"
        );
        if (calendarInstance) {
            calendarInstance.removeAllEvents();
        }
    };

    const populateForm = (schedule) => {
        clearFormErrors();
        $form[0].reset();

        $("#schedule_title").val(schedule?.title || "");
        $("#schedule_color").val(schedule?.color || "#3788D8");
        $timeIn.val(schedule?.time_in || "");
        $timeOut.val(schedule?.time_out || "");
        $("#schedule_description").val(schedule?.description || "");

        $dayCheckboxes.each(function () {
            const value = Number($(this).val());
            $(this).prop(
                "checked",
                Array.isArray(schedule?.days_of_week) &&
                    schedule.days_of_week.includes(value)
            );
        });

        $scheduleIdInput.val(schedule?.id || "");
        $employeeIdInput.val(currentEmployeeId ?? $employeeIdInput.val());
        currentSchedule = schedule || null;
        $deleteBtn.prop("disabled", !currentSchedule);

        if ($dayCheckboxes.filter(":checked").length === 6) {
            $daysError.addClass("d-none");
        }

        if (calendarInstance) {
            calendarInstance.removeAllEvents();
            if (schedule) {
                calendarInstance.addEvent({
                    id: `schedule-${schedule.id}`,
                    title: schedule.title || "Scheduled Shift",
                    daysOfWeek: schedule.days_of_week || [],
                    startTime: schedule.time_in,
                    endTime: schedule.time_out,
                    color: schedule.color || "#3788D8",
                    textColor: "#ffffff",
                });
                setCalendarBadge(
                    "Previewing weekly shift",
                    "badge bg-primary text-white"
                );
            } else {
                setCalendarBadge(
                    "No schedule loaded",
                    "badge bg-primary-subtle text-primary"
                );
            }
        }
    };

    const highlightSelectedEmployee = () => {
        if (!currentEmployeeId) return;
        $employeeList.find(".list-group-item").each(function () {
            const isActive =
                Number($(this).data("employeeId")) ===
                Number(currentEmployeeId);
            $(this).toggleClass("active", isActive);
        });
    };

    const renderEmployees = (employees) => {
        $employeeList.empty();
        if (!employees.length) {
            $employeeList.append(
                $("<div>", {
                    class: "list-group-item text-center text-muted py-4",
                    text: "No employees found.",
                })
            );
            return;
        }

        employees.forEach((employee) => {
            const metaParts = [];
            if (employee.position) metaParts.push(employee.position);
            if (employee.department) metaParts.push(employee.department);

            const $item = $(`
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                    <div class="me-2">
                        <div class="fw-semibold">${employee.name}</div>
                        <small class="text-muted">${metaParts.join(
                            " • "
                        )}</small>
                    </div>
                    <span class="badge ${
                        employee.has_schedule
                            ? "bg-success-subtle text-success"
                            : "bg-light text-muted border"
                    }">
                        ${
                            employee.has_schedule
                                ? "Has schedule"
                                : "No schedule"
                        }
                    </span>
                </button>
            `);

            $item.data("employeeId", employee.id);
            $item.on("click", () => loadEmployee(employee.id));
            $employeeList.append($item);
        });

        highlightSelectedEmployee();
    };

    const fetchEmployees = (search = "") => {
        const settings = search ? { data: { search } } : {};
        requestJson("/human-resources/schedules/manage/employees", settings)
            .then((response) => {
                renderEmployees(response.data || []);
            })
            .catch((error) => {
                console.error(error);
                renderEmployees([]);
                showToast(
                    "Unable to load employees",
                    "Please refresh and try again.",
                    "error"
                );
            });
    };

    const loadEmployee = (employeeId) => {
        if (!employeeId) return;
        currentEmployeeId = employeeId;
        $employeeIdInput.val(employeeId);
        setFormDisabled(true);
        clearForm();
        highlightSelectedEmployee();
        setStatusBadge("Loading schedule…", "badge bg-warning text-dark");
        setCalendarBadge("Loading schedule…", "badge bg-warning text-dark");

        requestJson(`/human-resources/schedules/manage/employees/${employeeId}`)
            .then((payload) => {
                const { employee, schedule } = payload;
                $employeeLabel.text(
                    employee
                        ? `${employee.name} — ${[
                              employee.position,
                              employee.department,
                          ]
                              .filter(Boolean)
                              .join(" • ")}`
                        : "Employee selected"
                );
                setStatusBadge(
                    schedule ? "Schedule loaded" : "No schedule yet",
                    schedule
                        ? "badge bg-success-subtle text-success"
                        : "badge bg-light text-muted border"
                );
                setCalendarBadge(
                    schedule ? "Previewing weekly shift" : "No schedule loaded",
                    schedule
                        ? "badge bg-primary text-white"
                        : "badge bg-primary-subtle text-primary"
                );
                populateForm(schedule);
                setFormDisabled(false);
            })
            .catch((error) => {
                console.error(error);
                showToast(
                    "Failed to load schedule",
                    "Please try again.",
                    "error"
                );
                $employeeLabel.text("Unable to load employee details.");
                setStatusBadge("Error", "badge bg-danger text-white");
                setCalendarBadge("Error", "badge bg-danger text-white");
                setFormDisabled(true);
            });
    };

    const getPayloadFromForm = () => ({
        schedule_title: $("#schedule_title").val() || null,
        color: $("#schedule_color").val() || null,
        time_in: $timeIn.val(),
        time_out: $timeOut.val(),
        description: $("#schedule_description").val() || null,
        days_of_week: $dayCheckboxes
            .filter(":checked")
            .map((_, el) => Number($(el).val()))
            .get(),
    });

    const displayValidationErrors = (errors) => {
        clearFormErrors();
        $.each(errors || {}, (field, messages) => {
            const message = Array.isArray(messages) ? messages[0] : messages;

            if (field === "days_of_week" || field.startsWith("days_of_week.")) {
                $daysError.text(message).removeClass("d-none");
                $dayCheckboxes.addClass("is-invalid");
                return;
            }

            const $field = $(`#${field}`);
            const $namedField = $form.find(`[name="${field}"]`);
            const $target = $field.length ? $field : $namedField;

            if ($target.length) {
                $target.addClass("is-invalid");
                const $feedback =
                    $(`#${field}_error`) ||
                    $target
                        .closest(".form-group")
                        .find(".invalid-feedback")
                        .first() ||
                    $target.parent().find(".invalid-feedback").first();
                if ($feedback.length) {
                    $feedback.text(message).removeClass("d-none");
                }
            }
        });
    };

    const validateTimeFields = () => {
        $timeIn.removeClass("is-invalid");
        $timeOut.removeClass("is-invalid");
        $timeInError.addClass("d-none").text("");
        $timeOutError.addClass("d-none").text("");

        const start = $timeIn.val();
        const end = $timeOut.val();

        if (!start || !end) return true;

        const startMinutes = parseTimeToMinutes(start);
        const endMinutes = parseTimeToMinutes(end);

        if (startMinutes === null || endMinutes === null) return false;

        if (endMinutes <= startMinutes) {
            $timeOut.addClass("is-invalid");
            $timeOutError
                .text("End time must be after start time.")
                .removeClass("d-none");
            return false;
        }

        if (endMinutes - startMinutes < 300) {
            $timeOut.addClass("is-invalid");
            $timeOutError
                .text("Shift must be at least 5 hours long.")
                .removeClass("d-none");
            return false;
        }

        return true;
    };

    const saveSchedule = () => {
        if (!currentEmployeeId) return;

        const payload = getPayloadFromForm();
        clearFormErrors();

        if (payload.days_of_week.length !== 6) {
            $daysError
                .text("Please select exactly six working days.")
                .removeClass("d-none");
            $dayCheckboxes.addClass("is-invalid");
            return;
        }

        if (!validateTimeFields()) {
            return;
        }
        $("#LoadingScreen").fadeIn(200);
        setStatusBadge("Saving…", "badge bg-warning text-dark");
        setFormDisabled(true);

        requestJson(
            `/human-resources/schedules/manage/employees/${currentEmployeeId}`,
            {
                method: "POST",
                body: JSON.stringify(payload),
                contentType: "application/json; charset=utf-8",
                processData: false,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
            }
        )
            .then((response) => {
                $("#LoadingScreen").fadeOut(200);
                showToast(
                    "Schedule saved",
                    response.message || "The schedule has been updated.",
                    "success"
                );
                populateForm(response.schedule);
                setStatusBadge(
                    "Schedule saved",
                    "badge bg-success-subtle text-success"
                );
                setCalendarBadge(
                    "Previewing weekly shift",
                    "badge bg-primary text-white"
                );
                setFormDisabled(false);
                fetchEmployees($employeeSearch.val());
            })
            .catch((error) => {
                $("#LoadingScreen").fadeOut(200);
                console.error(error);
                setStatusBadge(
                    "Error saving schedule",
                    "badge bg-danger text-white"
                );
                setFormDisabled(false);
                if (error.status === 422) {
                    displayValidationErrors(error.payload?.errors);
                    showToast(
                        "Validation error",
                        "Please check the highlighted fields.",
                        "error"
                    );
                } else {
                    showToast(
                        "Unable to save schedule",
                        error.payload?.message || "Please try again later.",
                        "error"
                    );
                }
            });
    };

    const deleteSchedule = () => {
        if (!currentSchedule?.id) return;

        confirmAction({
            title: "Remove schedule?",
            text: "This will detach the schedule from the employee.",
            icon: "warning",
            confirmText: "Yes, remove it",
        }).then((confirmed) => {
            if (!confirmed) return;
            $("#LoadingScreen").fadeIn(200);
            setStatusBadge("Removing…", "badge bg-warning text-dark");
            setFormDisabled(true);
            requestJson(
                `/human-resources/schedules/manage/schedule/${currentSchedule.id}`,
                {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                }
            )
                .then((response) => {
                    $("#LoadingScreen").fadeOut(200);
                    showToast(
                        "Schedule removed",
                        response.message || "The schedule was removed.",
                        "success"
                    );
                    clearForm();
                    setFormDisabled(false);
                    setStatusBadge(
                        "No schedule",
                        "badge bg-light text-muted border"
                    );
                    setCalendarBadge(
                        "No schedule loaded",
                        "badge bg-primary-subtle text-primary"
                    );
                    fetchEmployees($employeeSearch.val());
                })
                .catch((error) => {
                    $("#LoadingScreen").fadeOut(200);
                    console.error(error);
                    setFormDisabled(false);
                    setStatusBadge(
                        "Error removing schedule",
                        "badge bg-danger text-white"
                    );
                    showToast(
                        "Unable to remove schedule",
                        error.payload?.message || "Please try again later.",
                        "error"
                    );
                });
        });
    };

    $dayCheckboxes.on("change", function () {
        const checkedCount = $dayCheckboxes.filter(":checked").length;
        if (checkedCount > 6) {
            $(this).prop("checked", false);
            showToast(
                "Selection limit",
                "Select exactly six working days.",
                "warning"
            );
            return;
        }

        if (checkedCount === 6) {
            $daysError.addClass("d-none");
            $dayCheckboxes.removeClass("is-invalid");
        }
    });

    $form.on("submit", (event) => {
        event.preventDefault();
        confirmAction({
            title: "Save schedule?",
            text: "This will update the employee’s weekly shift.",
            icon: "question",
            confirmText: "Yes, save it",
        }).then((confirmed) => {
            if (!confirmed) return;
            saveSchedule();
        });
    });

    $resetBtn.on("click", (event) => {
        event.preventDefault();
        confirmAction({
            title: "Reset changes?",
            text: "Any unsaved changes will be lost.",
            icon: "question",
            confirmText: "Yes, reset",
        }).then((confirmed) => {
            if (!confirmed) return;
            populateForm(currentSchedule);
            clearFormErrors();
            $dayCheckboxes.removeClass("is-invalid");
        });
    });

    $deleteBtn.on("click", (event) => {
        event.preventDefault();
        deleteSchedule();
    });

    $employeeSearch.on("input", () => {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchEmployees($employeeSearch.val().trim());
        }, 250);
    });

    $timeIn.on("change blur", validateTimeFields);
    $timeOut.on("change blur", validateTimeFields);

    const initialView = window.innerWidth < 768 ? "listWeek" : "timeGridWeek";
    if (calendarEl && typeof FullCalendar !== "undefined") {
        calendarInstance = new FullCalendar.Calendar(calendarEl, {
            height: "auto",
            initialView,
            themeSystem: "bootstrap5",
            headerToolbar: {
                start: "prev,next today",
                center: "title",
                end: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
            },
            slotMinTime: "05:00:00",
            slotMaxTime: "23:00:00",
            nowIndicator: true,
            dayMaxEvents: true,
            editable: false,
            selectable: false,
            buttonText: {
                today: "Today",
                month: "Month",
                week: "Week",
                day: "Day",
                list: "Agenda",
            },
        });
        calendarInstance.render();

        $(window).on("resize", () => {
            if (!calendarInstance) return;
            const view = window.innerWidth < 768 ? "listWeek" : "timeGridWeek";
            if (calendarInstance.view.type !== view) {
                calendarInstance.changeView(view);
            }
        });
    }

    setFormDisabled(true);
    fetchEmployees();
});
