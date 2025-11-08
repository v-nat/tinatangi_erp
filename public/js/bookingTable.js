$(document).ready(function () {
    const $form = $("#booking-form");
    const $loading = $("#loading-message");
    const $error = $("#error-message");
    const $sent = $("#sent-message");
    const $submitButton = $form.find('button[type="submit"]');

    const $dateInput = $("#date");
    const $timeInput = $("#time");
    const $phoneField = $("#phone");
    const $emailField = $("#email");
    const $peopleField = $("#people");

    const $phoneFeedback = $phoneField.siblings(".invalid-feedback");
    const $emailFeedback = $emailField.siblings(".invalid-feedback");
    const $dateFeedback = $dateInput.siblings(".invalid-feedback");
    const $peopleFeedback = $peopleField.siblings(".invalid-feedback");
    const $timeFeedback = $timeInput.siblings(".invalid-feedback");

    const phoneRegex = /^(09|\+639)\d{9}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function getTodayDate() {
        var d = new Date();
        var month = (d.getMonth() + 1).toString().padStart(2, "0");
        var day = d.getDate().toString().padStart(2, "0");
        var year = d.getFullYear();
        return year + "-" + month + "-" + day;
    }

    function getCurrentTime() {
        return new Date().toTimeString().slice(0, 5);
    }

    const today = getTodayDate();
    const todayMidnight = new Date();
    todayMidnight.setHours(0, 0, 0, 0);
    $dateInput.attr("min", today);

    function updateSubmitButtonState() {
        const hasErrors = $form.find('.is-invalid').length > 0;
        $submitButton.prop('disabled', hasErrors);
    }

    function updateMinTime() {
        var selectedDate = $dateInput.val();
        var currentTime = getCurrentTime();

        if (selectedDate === today) {
            $timeInput.attr("min", currentTime);
        } else {
            $timeInput.removeAttr("min");
        }
    }

    function validateBookingTime() {
        const dateStr = $dateInput.val();
        const timeStr = $timeInput.val();

        if (!dateStr || !timeStr) {
            $timeInput.removeClass("is-invalid");
            $timeFeedback.hide();
            return;
        }

        const selectedDate = new Date(dateStr + "T00:00:00");

        if (selectedDate.getTime() === todayMidnight.getTime()) {
            const now = new Date();
            const minTime = new Date(now.getTime() + 30 * 60000);
            const selectedDateTime = new Date(dateStr + "T" + timeStr);

            if (selectedDateTime < minTime) {
                $timeInput.addClass("is-invalid");
                $timeFeedback.show().text("For today, time must be at least 30 minutes from now.");
            } else {
                $timeInput.removeClass("is-invalid");
                $timeFeedback.hide();
            }
        } else {
            $timeInput.removeClass("is-invalid");
            $timeFeedback.hide();
        }
        updateSubmitButtonState();
    }

    updateMinTime();

    $('input[type="tel"]').on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^\d+]/g, "")
        );
    });

    $phoneField.on("input", function () {
        const currentValue = $phoneField.val();
        if (!currentValue) {
            $phoneField.removeClass("is-invalid");
            $phoneFeedback.hide();
        } else if (!phoneRegex.test(currentValue)) {
            $phoneField.addClass("is-invalid");
            $phoneFeedback
                .show()
                .text("Must be a valid 11-digit PH number (e.g., 09... or +639...).");
        } else {
            $phoneField.removeClass("is-invalid");
            $phoneFeedback.hide();
        }
        updateSubmitButtonState();
    });

    $emailField.on("input", function () {
        const email = $emailField.val();
        if (email === "") {
            $emailField.removeClass("is-invalid");
            $emailFeedback.hide();
        } else if (!emailRegex.test(email)) {
            $emailField.addClass("is-invalid");
            $emailFeedback.show().text("Please enter a valid email address.");
        } else {
            $emailField.removeClass("is-invalid");
            $emailFeedback.hide();
        }
        updateSubmitButtonState();
    });

    $peopleField.on("input", function () {
        const people = $peopleField.val();
        const peopleInt = parseInt(people);

        if (people === "") {
            $peopleField.removeClass("is-invalid");
            $peopleFeedback.hide();
        } else if (isNaN(peopleInt) || peopleInt <= 0) {
            $peopleField.addClass("is-invalid");
            $peopleFeedback.show().text("Please enter a valid number (at least 1).");
        } else {
            $peopleField.removeClass("is-invalid");
            $peopleFeedback.hide();
        }
        updateSubmitButtonState();
    });

    $timeInput.on("input", validateBookingTime);

    $dateInput.on("change", function () {
        updateMinTime();

        const dateStr = $dateInput.val();
        if (dateStr === "") {
            $dateInput.removeClass("is-invalid");
            $dateFeedback.hide();
        } else {
            const selectedDate = new Date(dateStr + "T00:00:00");
            if (selectedDate < todayMidnight) {
                $dateInput.addClass("is-invalid");
                $dateFeedback.show().text("The booking date cannot be in the past.");
            } else {
                $dateInput.removeClass("is-invalid");
                $dateFeedback.hide();
            }
        }

        validateBookingTime();
        updateSubmitButtonState();
    });

    $form.on("submit", function (e) {
        e.preventDefault();

        $loading.hide();
        $error.hide();
        $sent.hide();

        let isValid = true;
        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find("input[required], select[required]").each(function () {
            const $field = $(this);
            if (!$field.val() || ($field.is("input") && !$field.val().trim())) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        if (!isValid) return;

        Swal.fire({
            title: "Submit Reservation?",
            text: "Do you confirm the details of reservation are correct?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm!",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $loading.show();
            const formMethod = $form.attr("method");

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                type: formMethod,
                url: `/book-a-table`,
                data: $form.serialize(),
                success: function (response) {
                    $loading.hide();
                    $sent.show();
                    $form[0].reset();
                    updateMinTime();
                    setTimeout(function () {
                        $sent.hide();
                    }, 3000);
                },
                error: function (xhr) {
                    $loading.hide();
                    const serverError = xhr.responseText || "An error occurred. Please try again later.";
                    $error.html(serverError).show();
                },
            });
        });
    });
});
