$(document).ready(function () {
    const $form = $("#booking-form");
    const $loading = $("#loading-message");
    const $error = $("#error-message");
    const $sent = $("#sent-message");

    function getTodayDate() {
        var d = new Date();
        var month = (d.getMonth() + 1).toString().padStart(2, "0");
        var day = d.getDate().toString().padStart(2, "0");
        var year = d.getFullYear();
        return year + "-" + month + "-" + day;
    }

    function getCurrentTime() {
        var d = new Date();
        var hours = d.getHours().toString().padStart(2, "0");
        var minutes = d.getMinutes().toString().padStart(2, "0");
        return hours + ":" + minutes;
    }

    var $dateInput = $("#date");
    var $timeInput = $("#time");
    var today = getTodayDate();
    $dateInput.attr("min", today);

    function updateMinTime() {
        var selectedDate = $dateInput.val();
        var currentTime = getCurrentTime();

        if (selectedDate === today) {
            $timeInput.attr("min", currentTime);
        } else {
            $timeInput.removeAttr("min");
        }
    }
    $dateInput.on("change", updateMinTime);
    updateMinTime();

    $('input[type="tel"]').on("input", function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^\d+]/g, "")
        );
    });

    $form.on("submit", async function (e) {
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

    let errors = [];

    const name = $("#name").val().trim();
    const email = $("#email").val().trim();
    const phone = $("#phone").val().trim();
    const date = $("#date").val();
    const time = $("#time").val();
    const people = $("#people").val().trim();

    if (email !== "" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push("Please enter a valid email address.");
    }

    const phoneField = $("#phone");
    if (phoneField.val() && !/^(09|\+639)\d{9}$/.test(phoneField.val())) {
        phoneField
            .addClass("is-invalid")
            .siblings(".invalid-feedback")
            .show()
            .text(
                "Phone Number must be a valid 11-digit PH number (09...)."
            );
        isValid = false;
    }

    if (date !== "") {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const selectedDate = new Date(date + "T00:00:00");

        if (selectedDate < today) {
            errors.push("The booking date cannot be in the past.");
        }
    }

    if (people !== "" && (isNaN(people) || parseInt(people) <= 0)) {
        errors.push("Please enter a valid number of people (at least 1).");
    }

    if (!isValid || errors.length > 0) {
        $error.html(errors.join("<br>")).show();
        return;
    }

    $loading.show();
    let token;
    try {
        await grecaptcha.enterprise.ready();

        token = await grecaptcha.enterprise.execute('6Lc_hwUsAAAAAIz3G4ruHeuYV1A27ZvCDZAs3Ry0', { action: 'BOOKING' });

    } catch (err) {
        console.error("reCAPTCHA execution error:", err);
        $loading.hide();
        $error.html("Could not verify your request. Please try again.").show();
        return;
    }

    const formData = $form.serialize();
    const dataWithToken = formData + "&g-recaptcha-response=" + encodeURIComponent(token);

    const formMethod = $form.attr("method");

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                "content"
            ),
        },
        type: formMethod,
        url: `/book-a-table`,
        data: dataWithToken,
        success: function (response) {
            $loading.hide();
            $sent.show();
            $form[0].reset();
            setTimeout(function () {
                $sent.hide();
            }, 3000);
        },
        error: function (xhr) {
            $loading.hide();
            let serverError =
                "An error occurred. Please try again later.";
            if (xhr.responseText) {
                serverError = xhr.responseText;
            }
            $error.html(serverError).show();
        },
    });
});
});
