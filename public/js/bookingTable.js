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

    const tableModal = new bootstrap.Modal(
        document.getElementById("tableRecommendationModal")
    );
    const $tableOptionsContainer = $("#table-options-container");
    const $confirmTableBookingBtn = $("#confirmTableBookingBtn");

    let bookingData = {};

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
        const hasErrors = $form.find(".is-invalid").length > 0;
        $submitButton.prop("disabled", hasErrors);
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
                $timeFeedback
                    .show()
                    .text(
                        "For today, time must be at least 30 minutes from now."
                    );
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
                .text(
                    "Must be a valid 11-digit PH number (e.g., 09... or +639...)."
                );
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
            $peopleFeedback
                .show()
                .text("Please enter a valid number (at least 1).");
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
                $dateFeedback
                    .show()
                    .text("The booking date cannot be in the past.");
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
                $field
                    .siblings(".invalid-feedback")
                    .show()
                    .text("This field is required.");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        validateBookingTime();
        $phoneField.trigger("input");
        $emailField.trigger("input");
        $peopleField.trigger("input");
        $dateInput.trigger("change");

        if ($form.find(".is-invalid").length > 0) {
            isValid = false;
        }

        if (!isValid) {
            $error.html("Please fix the errors in the form.").show();
            return;
        }
        $("#LoadingScreen").fadeIn(200);
        $loading.show();
        $submitButton.prop("disabled", true);

        bookingData = $form.serialize();

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: `/book-a-table/check-availability`,
            data: {
                date: $dateInput.val(),
                time: $timeInput.val(),
                people: $peopleField.val(),
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                $loading.hide();
                $submitButton.prop("disabled", false);

                populateTableModal(response.tables);
                tableModal.show();
            },
            error: function (xhr) {
                $loading.hide();
                $submitButton.prop("disabled", false);
                const serverError =
                    xhr.responseJSON?.error ||
                    xhr.responseText ||
                    "An error occurred. Please try again later.";
                $error.html(serverError).show();
            },
        });
    });

    function populateTableModal(tables) {
        $tableOptionsContainer.empty();
        if (!tables || tables.length === 0) {
            $tableOptionsContainer.html(
                '<p class="text-center">No tables found matching your criteria.</p>'
            );
            $confirmTableBookingBtn.prop("disabled", true);
            return;
        }

        $confirmTableBookingBtn.prop("disabled", false);

        tables.forEach((table, index) => {
            const imageUrl = table.image
                ? `/storage/app/public/${table.image}`
                : "https://placehold.co/600x400/EEE/31343C?text=No+Image";

            const cardHtml = `
                <div class="col-md-6 col-lg-4">
                    <div class="table-choice-card">
                        <input type="radio" name="table_id" id="table_${
                            table.id
                        }" class="d-none" value="${table.id}" ${
                index === 0 ? "checked" : ""
            }>
                        <label for="table_${table.id}" class="card">
                            <img src="${imageUrl}" class="card-img-top" alt="${
                table.name
            }">
                            <div class="card-body">
                                <h5 class="card-title">${table.name}</h5>
                                <p class="card-text mb-0"><strong>Location:</strong> ${
                                    table.location
                                }</p>
                                <p class="card-text mb-0"><strong>Capacity:</strong> ${
                                    table.capacity
                                } people</p>
                                <p class="card-text"><small class="text-muted">${
                                    table.description || ""
                                }</small></p>
                            </div>
                            <div class="card-footer text-center">
                                Select this Table
                            </div>
                        </label>
                    </div>
                </div>
            `;
            $tableOptionsContainer.append(cardHtml);
        });

        const styleTag = `
            <style>
                .table-choice-card .card {
                    cursor: pointer;
                    border: 3px solid transparent;
                    transition: all 0.3s ease;
                }
                .table-choice-card .card:hover {
                    border-color: var(--accent-color-light);
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .table-choice-card input[type="radio"]:checked + .card {
                    border-color: var(--accent-color);
                    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                }
                .table-choice-card input[type="radio"]:checked + .card .card-footer {
                    background-color: var(--accent-color);
                    color: white;
                    font-weight: bold;
                }
                .table-choice-card .card-footer {
                    background-color: #f8f9fa;
                    transition: all 0.3s ease;
                }
            </style>
        `;
        if (!$("style#tableCardStyle").length) {
            $("head").append(styleTag);
        }
    }

    $confirmTableBookingBtn.on("click", function () {
        const $selectedTable = $tableOptionsContainer.find(
            'input[name="table_id"]:checked'
        );

        if ($selectedTable.length === 0) {
            Swal.fire("Error", "Please select a table.", "error");
            return;
        }
        Swal.fire({
            title: "Submit Booking?",
            text: "Please confirm to finalize your table booking.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Log Out",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);

            const tableId = $selectedTable.val();
            const finalBookingData = bookingData + `&table_id=${tableId}`;

            $(this).prop("disabled", true).text("Booking...");
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                type: "POST",
                url: `/book-a-table`,
                data: finalBookingData,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    tableModal.hide();
                    $sent.html(response.success).show();
                    $form[0].reset();
                    updateMinTime();
                    setTimeout(function () {
                        $sent.hide();
                    }, 5000);
                },
                error: function (xhr) {
                    const serverError =
                        xhr.responseJSON?.error ||
                        xhr.responseText ||
                        "An error occurred. Please try again later.";
                    $error.html(serverError).show();
                },
                complete: function () {
                    $confirmTableBookingBtn
                        .prop("disabled", false)
                        .text("Confirm Booking");
                },
            });
        });
    });
});
