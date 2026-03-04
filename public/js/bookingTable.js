$(document).ready(function () {
    const $form          = $("#booking-form");
    const $loading       = $("#loading-message");
    const $error         = $("#error-message");
    const $sent          = $("#sent-message");
    const $submitButton  = $form.find('button[type="submit"]');

    const $nameInput    = $("#name");
    const $dateInput    = $("#date");
    const $timeInput    = $("#time");
    const $phoneField   = $("#phone");
    const $emailField   = $("#email");
    const $peopleField  = $("#people");

    const $phoneFeedback  = $phoneField.siblings(".invalid-feedback");
    const $emailFeedback  = $emailField.siblings(".invalid-feedback");
    const $dateFeedback   = $dateInput.siblings(".invalid-feedback");
    const $peopleFeedback = $peopleField.siblings(".invalid-feedback");
    const $timeFeedback   = $timeInput.siblings(".invalid-feedback");

    const categoryModal = new bootstrap.Modal(document.getElementById("tableRecommendationModal"));
    const slotModal     = new bootstrap.Modal(document.getElementById("tableSlotModal"), { backdrop: "static" });

    const $tableOptionsContainer = $("#table-options-container");
    const $slotGrid              = $("#slot-grid");
    const $slotLoading           = $("#slot-loading");
    const $slotError             = $("#slot-error");
    const $slotCategoryName      = $("#slotCategoryName");
    const $slotDurationSelect    = $("#slotDurationSelect");
    const $confirmSlotBtn        = $("#confirmSlotBtn");
    const $slotBackBtn           = $("#slotBackBtn");

    let bookingData      = {};
    let selectedTableId  = null;
    let selectedSlot     = null;

    const phoneRegex = /^(09|\+639)\d{9}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // ── Date / Time helpers ──────────────────────────────────────────────

    function getTodayDate() {
        const d     = new Date();
        const month = (d.getMonth() + 1).toString().padStart(2, "0");
        const day   = d.getDate().toString().padStart(2, "0");
        return d.getFullYear() + "-" + month + "-" + day;
    }

    function getCurrentTime() {
        return new Date().toTimeString().slice(0, 5);
    }

    const today        = getTodayDate();
    const todayMidnight = new Date();
    todayMidnight.setHours(0, 0, 0, 0);
    $dateInput.attr("min", today);

    function updateSubmitButtonState() {
        $submitButton.prop("disabled", $form.find(".is-invalid").length > 0);
    }

    function updateMinTime() {
        if ($dateInput.val() === today) {
            $timeInput.attr("min", getCurrentTime());
        } else {
            $timeInput.removeAttr("min");
        }
    }

    function validateBookingTime() {
        const dateStr = $dateInput.val();
        const timeStr = $timeInput.val();
        if (!dateStr || !timeStr) { $timeInput.removeClass("is-invalid"); $timeFeedback.hide(); return; }

        const selectedDate = new Date(dateStr + "T00:00:00");
        if (selectedDate.getTime() === todayMidnight.getTime()) {
            const minTime         = new Date(new Date().getTime() + 30 * 60000);
            const selectedDateTime = new Date(dateStr + "T" + timeStr);
            if (selectedDateTime < minTime) {
                $timeInput.addClass("is-invalid");
                $timeFeedback.show().text("For today, time must be at least 30 minutes from now.");
            } else {
                $timeInput.removeClass("is-invalid"); $timeFeedback.hide();
            }
        } else {
            $timeInput.removeClass("is-invalid"); $timeFeedback.hide();
        }
        updateSubmitButtonState();
    }

    updateMinTime();

    // ── Field validation listeners ───────────────────────────────────────

    $('input[type="tel"]').on("input", function () {
        $(this).val($(this).val().replace(/[^\d+]/g, ""));
    });

    $nameInput.on("input", function () {
        const name = $nameInput.val().trim();
        if (!name) {
            $nameInput.addClass("is-invalid");
            $nameInput.siblings(".invalid-feedback").show().text("Name is required.");
        } else {
            $nameInput.removeClass("is-invalid");
            $nameInput.siblings(".invalid-feedback").hide();
        }
        updateSubmitButtonState();
    });

    $phoneField.on("input", function () {
        const v = $phoneField.val();
        if (!v) {
            $phoneField.removeClass("is-invalid"); $phoneFeedback.hide();
        } else if (!phoneRegex.test(v)) {
            $phoneField.addClass("is-invalid");
            $phoneFeedback.show().text("Must be a valid 11-digit PH number (e.g., 09... or +639...).");
        } else {
            $phoneField.removeClass("is-invalid"); $phoneFeedback.hide();
        }
        updateSubmitButtonState();
    });

    $emailField.on("input", function () {
        const v = $emailField.val();
        if (!v) {
            $emailField.removeClass("is-invalid"); $emailFeedback.hide();
        } else if (!emailRegex.test(v)) {
            $emailField.addClass("is-invalid");
            $emailFeedback.show().text("Please enter a valid email address.");
        } else {
            $emailField.removeClass("is-invalid"); $emailFeedback.hide();
        }
        updateSubmitButtonState();
    });

    $peopleField.on("input", function () {
        const v   = $peopleField.val();
        const int = parseInt(v);
        if (!v) {
            $peopleField.removeClass("is-invalid"); $peopleFeedback.hide();
        } else if (isNaN(int) || int <= 0) {
            $peopleField.addClass("is-invalid");
            $peopleFeedback.show().text("Please enter a valid number (at least 1).");
        } else {
            $peopleField.removeClass("is-invalid"); $peopleFeedback.hide();
        }
        updateSubmitButtonState();
    });

    $timeInput.on("input", validateBookingTime);

    $dateInput.on("change", function () {
        updateMinTime();
        const dateStr = $dateInput.val();
        if (!dateStr) {
            $dateInput.removeClass("is-invalid"); $dateFeedback.hide();
        } else if (new Date(dateStr + "T00:00:00") < todayMidnight) {
            $dateInput.addClass("is-invalid");
            $dateFeedback.show().text("The booking date cannot be in the past.");
        } else {
            $dateInput.removeClass("is-invalid"); $dateFeedback.hide();
        }
        validateBookingTime();
        updateSubmitButtonState();
    });

    // ── Step 1: Form submit → check availability ─────────────────────────

    $form.on("submit", function (e) {
        e.preventDefault();
        $loading.hide(); $error.hide(); $sent.hide();

        $form.find(".is-invalid").removeClass("is-invalid");
        $form.find("input[required], select[required]").each(function () {
            const $f = $(this);
            if (!$f.val() || ($f.is("input") && !$f.val().trim())) {
                $f.addClass("is-invalid");
                $f.siblings(".invalid-feedback").show().text("This field is required.");
            }
        });

        validateBookingTime();
        $phoneField.trigger("input");
        $emailField.trigger("input");
        $peopleField.trigger("input");
        $dateInput.trigger("change");

        if ($form.find(".is-invalid").length > 0) {
            $error.html("Please fill in all required fields.").show();
            return;
        }

        $("#LoadingScreen").fadeIn(200);
        $loading.show();
        $submitButton.prop("disabled", true);
        bookingData = $form.serialize();

        $.ajax({
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            type: "POST",
            url: "/book-a-table/check-availability",
            data: {
                date:   $dateInput.val(),
                time:   $timeInput.val(),
                people: $peopleField.val(),
            },
            success: function (response) {
                $("#LoadingScreen").fadeOut(200);
                $loading.hide();
                $submitButton.prop("disabled", false);
                populateCategoryModal(response.tables);
                categoryModal.show();
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                $loading.hide();
                $submitButton.prop("disabled", false);
                $error.html(xhr.responseJSON?.error || "An error occurred. Please try again later.").show();
            },
        });
    });

    // ── Step 1: Render category cards ───────────────────────────────────

    function populateCategoryModal(tables) {
        $tableOptionsContainer.empty();

        if (!tables || tables.length === 0) {
            $tableOptionsContainer.html('<p class="text-center" style="color:#8a7d6a;">No tables found matching your criteria.</p>');
            return;
        }

        tables.forEach((table) => {
            const imageUrl = table.image
                ? `/storage/app/public/${table.image}`
                : "https://placehold.co/600x350/231e17/cda45e?text=No+Image";

            const card = `
                <div class="col-md-6 col-lg-4">
                    <div class="table-choice-card h-100" data-table-id="${table.id}" data-table-name="${table.name}">
                        <div class="cafe-table-card h-100">
                            <img src="${imageUrl}" alt="${table.name}" style="width:100%;height:155px;object-fit:cover;display:block;">
                            <div class="cafe-card-body">
                                <div class="cafe-card-title">${table.name}</div>
                                <div class="cafe-card-meta"><i class="fa-solid fa-location-dot me-1" style="color:#cda45e;font-size:.7rem;"></i>${table.location}</div>
                                <div class="cafe-card-meta"><i class="fa-solid fa-users me-1" style="color:#cda45e;font-size:.7rem;"></i>Up to ${table.capacity} guests</div>
                                ${table.description ? `<div class="cafe-card-desc">${table.description}</div>` : ""}
                            </div>
                            <div class="cafe-card-footer">Select &rarr;</div>
                        </div>
                    </div>
                </div>`;
            $tableOptionsContainer.append(card);
        });
    }

    // ── Click a category → open slot modal ──────────────────────────────

    $tableOptionsContainer.on("click", ".table-choice-card", function () {
        selectedTableId = $(this).data("table-id");
        const tableName = $(this).data("table-name");

        categoryModal.hide();

        $slotCategoryName.text(tableName);
        $slotDurationSelect.val("2");
        selectedSlot = null;
        $confirmSlotBtn.prop("disabled", true);

        slotModal.show();
        fetchSlots();
    });

    // ── Back button ──────────────────────────────────────────────────────

    $slotBackBtn.on("click", function () {
        slotModal.hide();
        categoryModal.show();
    });

    // ── Duration change → refresh slots ─────────────────────────────────

    $slotDurationSelect.on("change", function () {
        selectedSlot = null;
        $confirmSlotBtn.prop("disabled", true);
        fetchSlots();
    });

    // ── Fetch slots from server ──────────────────────────────────────────

    function fetchSlots() {
        $slotGrid.empty();
        $slotError.addClass("d-none").text("");
        $slotLoading.removeClass("d-none");

        $.ajax({
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            type: "GET",
            url: "/book-a-table/table-slots",
            data: {
                table_id:       selectedTableId,
                date:           $dateInput.val(),
                time:           $timeInput.val(),
                duration_hours: $slotDurationSelect.val(),
            },
            success: function (response) {
                $slotLoading.addClass("d-none");
                renderSlots(response.slots);
            },
            error: function (xhr) {
                $slotLoading.addClass("d-none");
                $slotError.removeClass("d-none").text(xhr.responseJSON?.error || "Could not load table slots.");
            },
        });
    }

    // ── Render slot buttons ──────────────────────────────────────────────

    function renderSlots(slots) {
        $slotGrid.empty();

        if (!slots || slots.length === 0) {
            $slotGrid.html('<p style="color:#8a7d6a;font-size:.85rem;">No table slots configured.</p>');
            return;
        }

        slots.forEach((slot) => {
            const available = slot.status === "available";
            const btn = $(`
                <button type="button"
                    class="cafe-slot-btn slot-btn ${available ? "" : "taken"}"
                    data-slot="${slot.number}"
                    ${available ? "" : "disabled aria-disabled='true'"}
                    title="${available ? "Available" : "Already booked"}">
                    Table ${slot.number}
                    <small>${available ? "Free" : "Taken"}</small>
                </button>
            `);

            if (available) {
                btn.on("click", function () {
                    $slotGrid.find(".slot-btn").removeClass("selected");
                    $(this).addClass("selected");
                    selectedSlot = slot.number;
                    $confirmSlotBtn.prop("disabled", false);
                });
            }

            $slotGrid.append(btn);
        });
    }

    // ── Step 2: Confirm booking ──────────────────────────────────────────

    $confirmSlotBtn.on("click", function () {
        if (!selectedSlot) {
            Swal.fire("Error", "Please select a table number.", "error");
            return;
        }

        Swal.fire({
            title: "Confirm Booking?",
            text: `Table ${selectedSlot} for ${$slotDurationSelect.val()} hour(s). This will finalize your reservation.`,
            icon: "warning",
            showCancelButton:    true,
            confirmButtonColor:  "#3085d6",
            cancelButtonColor:   "#d33",
            confirmButtonText:   "Confirm",
        }).then((result) => {
            if (!result.isConfirmed) return;

            $("#LoadingScreen").fadeIn(200);
            $confirmSlotBtn.prop("disabled", true).text("Booking...");

            const finalData = bookingData
                + `&table_id=${selectedTableId}`
                + `&table_number=${selectedSlot}`
                + `&duration_hours=${$slotDurationSelect.val()}`;

            $.ajax({
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                type: "POST",
                url: "/book-a-table",
                data: finalData,
                success: function (response) {
                    $("#LoadingScreen").fadeOut(200);
                    slotModal.hide();
                    $sent.html(response.success).show();
                    $form[0].reset();
                    updateMinTime();
                    selectedTableId = null;
                    selectedSlot    = null;
                    setTimeout(() => $sent.hide(), 5000);
                },
                error: function (xhr) {
                    $("#LoadingScreen").fadeOut(200);
                    $error.html(xhr.responseJSON?.error || "An error occurred. Please try again later.").show();
                },
                complete: function () {
                    $confirmSlotBtn.prop("disabled", false).text("Confirm Booking");
                },
            });
        });
    });
});
