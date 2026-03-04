import {
    formatDate,
    formatTime,
    formatToManilaTime,
} from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const csrfToken = $("meta[name='csrf-token']").attr("content");
    const STATUS_OPTIONS = {
        11: { label: "Pending", badgeClass: "bg-warning" },
        12: { label: "Rejected", badgeClass: "bg-danger" },
        13: { label: "Approved", badgeClass: "bg-success" },
        23: { label: "Completed", badgeClass: "bg-success" },
        31: { label: "Voided", badgeClass: "bg-danger" },
    };
    const UNKNOWN_BADGE = '<span class="badge bg-secondary">Unknown</span>';

    const $bookingViewModal = $("#bookingViewModal");
    const $bookingApproveModal = $("#bookingApproveModal");
    const $bookingRejectModal = $("#bookingRejectModal");
    const $bookingVoidModal = $("#bookingVoidModal");

    let tablesCache = null;

    const buildStatusBadge = (statusValue, fallback) => {
        const option = STATUS_OPTIONS[statusValue];
        if (!option) {
            return fallback || UNKNOWN_BADGE;
        }
        return `<span class="badge ${option.badgeClass}">${option.label}</span>`;
    };

    const setButtonLoading = (
        $button,
        isLoading,
        loadingText = "Processing..."
    ) => {
        if (isLoading) {
            $button.data("original-html", $button.html());
            $button
                .prop("disabled", true)
                .html(
                    `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>${loadingText}`
                );
        } else {
            const original = $button.data("original-html");
            if (original) {
                $button.html(original);
            }
            $button.prop("disabled", false);
        }
    };

    const fetchTables = () =>
        tablesCache
            ? Promise.resolve(tablesCache)
            : new Promise((resolve) => {
                  $.ajax({
                      url: "/customer-service/tables/list",
                      method: "GET",
                  })
                      .done((response) => {
                          tablesCache = response?.data ?? [];
                          resolve(tablesCache);
                      })
                      .fail(() => resolve([]));
              });

    const getRowData = (element) => {
        const $row = $(element).closest("tr");
        return (
            table.row($row).data() ||
            table.row($row.prev()).data() ||
            table.row($row.parent()).data()
        );
    };

    const getBookingId = (row) => {
        if (!row || typeof row !== "object") {
            return null;
        }
        return (
            row.id ??
            row.booking_id ??
            row.bookingId ??
            row?.booking?.id ??
            null
        );
    };

    const formatSchedule = (row) => {
        const dateText = row.date ? formatDate(row.date) : "Unknown date";
        const timeText = row.time ? formatTime(row.time) : "Unknown time";
        return `${dateText} • ${timeText}`;
    };

    const populateViewModal = (row) => {
        const bookingId = getBookingId(row);
        const contactPieces = [];
        if (row.phone) contactPieces.push(row.phone);
        if (row.email) contactPieces.push(row.email);

        $("#viewBookingName").text(row.name || "—");
        $("#viewBookingContact").text(
            contactPieces.length ? contactPieces.join(" • ") : "—"
        );
        $("#viewBookingGuests").text(row.people ?? "—");
        $("#viewBookingStatus").html(
            row.status_badge || buildStatusBadge(row.status)
        );
        $("#viewBookingDate").text(row.date ? formatDate(row.date) : "—");
        $("#viewBookingTime").text(row.time ? formatTime(row.time) : "—");
        $("#viewBookingDuration").text(row.duration_hours ? `${row.duration_hours} hr${row.duration_hours > 1 ? "s" : ""}` : "—");
        $("#viewBookingSlot").text(row.table_number ? `#${row.table_number}` : "—");

        const $tableImageWrapper = $("#viewBookingTableImageWrapper");
        const $tableImage = $("#viewBookingTableImage");

        if (row.table_for_reservation) {
            const tableInfo = row.table_for_reservation;
            $("#viewBookingTable").text(tableInfo.name || "—");
            const details = [];
            if (tableInfo.location)
                details.push(`Location: ${tableInfo.location}`);
            if (tableInfo.capacity)
                details.push(`Capacity: ${tableInfo.capacity}`);
            $("#viewBookingTableDetails").text(details.join(" • "));

            if (tableInfo.image) {
                const imageUrl = tableInfo.image.startsWith("http")
                    ? tableInfo.image
                    : `/storage/app/public/${tableInfo.image.replace(
                          /^\/|\\/g,
                          ""
                      )}`;
                $tableImage.attr("src", imageUrl).attr("alt", tableInfo.name);
                $tableImageWrapper.removeClass("d-none");
            } else {
                $tableImageWrapper.addClass("d-none");
                $tableImage.attr("src", "").attr("alt", "");
            }
        } else {
            $("#viewBookingTable").text("Not assigned");
            $("#viewBookingTableDetails").text("");
            $tableImageWrapper.addClass("d-none");
            $tableImage.attr("src", "").attr("alt", "");
        }

        const messageText = row.message?.trim()
            ? row.message
            : "No additional message.";
        const noteText = row.status_note?.trim()
            ? row.status_note
            : "No internal note recorded.";

        $("#viewBookingMessage").text(messageText);
        $("#viewBookingNote").text(noteText);
    };

    const populateApproveModal = (row) => {
        const bookingId = getBookingId(row);
        if (!bookingId) {
            return;
        }
        $("#approveBookingId").val(bookingId);
        $("#approveBookingCustomer").text(row.name || "—");
        $("#approveBookingSchedule").text(
            `${formatSchedule(row)} • ${row.people ?? "0"} guest(s)`
        );
        $("#approveNote").val(row.status_note || "");
        $("#approveTableSelect").empty();
        $("#approveTableAlert").addClass("d-none").text("");
        $("#approveBookingSubmit").prop("disabled", true);

        fetchTables().then((tables) => {
            const $select = $("#approveTableSelect");
            const guestCount = Number(row.people) || 0;

            const eligibleTables = tables.filter((table) => {
                const isActive = Number(table.status) === 1;
                const canFit = Number(table.capacity) >= guestCount;
                return isActive && canFit;
            });

            if (eligibleTables.length === 0) {
                $("#approveTableAlert")
                    .removeClass("d-none")
                    .text(
                        "No active tables can accommodate this party size. Please add or activate a table first."
                    );
                return;
            }

            eligibleTables
                .sort((a, b) => a.name.localeCompare(b.name))
                .forEach((table) => {
                    const optionText = `${table.name} • ${table.location} (Capacity: ${table.capacity}, Qty: ${table.quantity})`;
                    const $option = $("<option></option>")
                        .val(table.id)
                        .text(optionText);
                    if (Number(row.table_id) === Number(table.id)) {
                        $option.prop("selected", true);
                    }
                    $select.append($option);
                });

            if (!$select.val()) {
                $select.val(eligibleTables[0].id);
            }

            $("#approveBookingSubmit").prop("disabled", false);
        });
    };

    const populateRejectModal = (row) => {
        const bookingId = getBookingId(row);
        if (!bookingId) {
            return;
        }
        $("#rejectBookingId").val(bookingId);
        $("#rejectReason").val("");
        $("#rejectBookingSummary").text(
            `${row.name || "Unknown guest"} • ${formatSchedule(row)}`
        );
    };

    const populateVoidModal = (row) => {
        const bookingId = getBookingId(row);
        if (!bookingId) {
            return;
        }
        $("#voidBookingId").val(bookingId);
        $("#voidReason").val("");
        $("#voidBookingSummary").text(
            `${row.name || "Unknown guest"} • ${formatSchedule(row)}`
        );
    };

    const table = $("#bookings-table").DataTable({
        processing: true,
        serverSide: false,
        ajax: "/customer-service/bookings/all",
        order: [[0, "asc"]],
        columns: [
            {
                data: null,
                width: "5%",
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    if (type === "display") {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                    return meta.row + 1;
                },
            },
            { data: "name" },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <div>${row.phone || ""}</div>
                        <small>${row.email || ""}</small>
                    `;
                },
            },
            {
                data: "date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
            },
            {
                data: "time",
                render: function (data) {
                    return data ? formatTime(data) : "N/A";
                },
            },
            { data: "people", width: "5%" },
            {
                data: "table_for_reservation.name",
                render: function (data) {
                    return data ? data : '<span class="text-muted">N/A</span>';
                },
            },
            {
                data: null,
                className: "text-center",
                width: "160px",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.status_badge || buildStatusBadge(row.status);
                    }
                    if (type === "sort" || type === "filter") {
                        return (
                            row.status_label ||
                            STATUS_OPTIONS[row.status]?.label ||
                            ""
                        );
                    }
                    return row.status;
                },
            },
            {
                data: "created_at",
                render: function (data) {
                    return formatToManilaTime(data);
                },
                className: "dt-left",
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    const actionButtons = [];
                    const bookingId = getBookingId(row);

                    if (!bookingId) {
                        return `
                        <div class="action-btns justify-content-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <span class="btn btn-light btn-sm disabled">N/A</span>
                            </div>
                        </div>`;
                    }

                    actionButtons.push(
                        `<button type="button" class="btn icon btn-info btn-sm booking-action btn-view" data-action="view" data-id="${bookingId}" title="View">
                            <i class="fa-solid fa-eye"></i>
                        </button>`
                    );

                    if (String(row.status) === "11") {
                        actionButtons.push(
                            `<button type="button" class="btn icon btn-success btn-sm booking-action btn-approve" data-action="approve" data-id="${bookingId}" title="Approve">
                                <i class="fa-solid fa-check"></i>
                            </button>`
                        );
                        actionButtons.push(
                            `<button type="button" class="btn icon btn-danger btn-sm booking-action btn-reject" data-action="reject" data-id="${bookingId}" title="Reject">
                                <i class="fa-solid fa-xmark"></i>
                            </button>`
                        );
                    } else if (String(row.status) === "13") {
                        actionButtons.push(
                            `<button type="button" class="btn icon btn-warning btn-sm text-white booking-action btn-void" data-action="void" data-id="${bookingId}" title="Void">
                                <i class="fa-solid fa-ban"></i>
                            </button>`
                        );
                    }

                    return `
                        <div class="action-btns justify-content-center">
                            <div class="btn-group btn-group-sm" role="group">
                                ${actionButtons.join("")}
                            </div>
                        </div>
                    `;
                },
            },
            {
                data: "id",
                visible: false,
                searchable: false,
                orderable: false,
            },
        ],
        initComplete: function () {
            const api = this.api();
            const $statusFilter = $("#booking_status_filter");
            const $tableFilter = $("#booking_table_filter");

            const statusValues = new Set();
            const tableValues = new Set();

            api.rows()
                .data()
                .each(function (rowData) {
                    const statusLabel =
                        rowData.status_label ||
                        STATUS_OPTIONS[rowData.status]?.label ||
                        (rowData.status_badge
                            ? $("<div>").html(rowData.status_badge).text().trim()
                            : "");
                    if (statusLabel) {
                        statusValues.add(statusLabel);
                    }

                    const tableName =
                        rowData.table_for_reservation?.name ||
                        rowData.table_name ||
                        rowData.table ||
                        null;
                    if (tableName) {
                        tableValues.add(tableName);
                    }
                });

            Array.from(statusValues)
                .sort((a, b) => a.localeCompare(b))
                .forEach((status) => {
                    $statusFilter.append(
                        $("<option></option>").attr("value", status).text(status)
                    );
                });

            Array.from(tableValues)
                .sort((a, b) => a.localeCompare(b))
                .forEach((tableName) => {
                    $tableFilter.append(
                        $("<option></option>")
                            .attr("value", tableName)
                            .text(tableName)
                    );
                });
        },
    });

    $("#booking_status_filter").on("change", function () {
        const selectedStatus = $(this).val();
        table
            .column(7)
            .search(selectedStatus || "", false, false)
            .draw();
    });

    $("#booking_table_filter").on("change", function () {
        const selectedTable = $(this).val();
        table
            .column(6)
            .search(selectedTable || "", false, false)
            .draw();
    });

    $("#booking_date_filter").on("change", function () {
        const selectedDate = $(this).val();
        const formattedDate = selectedDate ? formatDate(selectedDate) : "";
        table
            .column(3)
            .search(formattedDate, false, false)
            .draw();
    });

    $("#bookings-table").on("click", ".booking-action", function () {
        const action = $(this).data("action");
        const row = getRowData(this);
        if (!row) return;
        const bookingId = getBookingId(row);

        if (!bookingId) {
            return;
        }

        if (action === "view") {
            populateViewModal(row);
            $bookingViewModal.modal("show");
            return;
        }
        if (action === "approve") {
            populateApproveModal(row);
            $bookingApproveModal.modal("show");
            return;
        }
        if (action === "reject") {
            populateRejectModal(row);
            $bookingRejectModal.modal("show");
            return;
        }
        if (action === "void") {
            populateVoidModal(row);
            $bookingVoidModal.modal("show");
        }
    });

    $("#approveBookingForm").on("submit", function (event) {
        event.preventDefault();

        const bookingId = $("#approveBookingId").val();
        if (!bookingId) {
            return;
        }
        const tableId = $("#approveTableSelect").val();
        const note = $("#approveNote").val();
        const $submitBtn = $("#approveBookingSubmit");

        if (!tableId) {
            $("#approveTableAlert")
                .removeClass("d-none")
                .text("Please select a table for this booking.");
            return;
        }

        $("#LoadingScreen").fadeIn(200);
        setButtonLoading($submitBtn, true, "Approving...");

        $.ajax({
            url: "/customer-service/bookings/approve",
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: {
                booking_id: bookingId,
                table_id: tableId,
                note: note,
            },
        })
            .done((response) => {
                $bookingApproveModal.modal("hide");
                Toast.fire({
                    icon: "success",
                    title: "Approved!",
                    text: response.success || "Booking approved successfully.",
                });
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                const errorMessage =
                    xhr.responseJSON?.error ||
                    xhr.responseJSON?.message ||
                    "Failed to approve booking. Please try again.";
                Swal.fire({
                    icon: "error",
                    title: "Approval Failed",
                    text: errorMessage,
                });
            })
            .always(() => {
                $("#LoadingScreen").fadeOut(200);
                setButtonLoading($submitBtn, false);
            });
    });

    $("#rejectBookingForm").on("submit", function (event) {
        event.preventDefault();

        const bookingId = $("#rejectBookingId").val();
        if (!bookingId) {
            return;
        }
        const reason = $("#rejectReason").val();
        const $submitBtn = $("#rejectBookingSubmit");
        $("#LoadingScreen").fadeIn(200);
        setButtonLoading($submitBtn, true, "Rejecting...");

        $.ajax({
            url: "/customer-service/bookings/reject",
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: {
                booking_id: bookingId,
                reason: reason,
            },
        })
            .done((response) => {
                $bookingRejectModal.modal("hide");
                Toast.fire({
                    icon: "success",
                    title: "Rejected!",
                    text: response.success || "Booking rejected successfully.",
                });
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                const errorMessage =
                    xhr.responseJSON?.error ||
                    xhr.responseJSON?.message ||
                    "Failed to reject booking. Please try again.";
                Swal.fire({
                    icon: "error",
                    title: "Rejection Failed",
                    text: errorMessage,
                });
            })
            .always(() => {
                $("#LoadingScreen").fadeOut(200);
                setButtonLoading($submitBtn, false);
            });
    });

    $("#voidBookingForm").on("submit", function (event) {
        event.preventDefault();

        const bookingId = $("#voidBookingId").val();
        if (!bookingId) {
            return;
        }
        const reason = $("#voidReason").val();
        const $submitBtn = $("#voidBookingSubmit");

        $("#LoadingScreen").fadeIn(200);
        setButtonLoading($submitBtn, true, "Voiding...");

        $.ajax({
            url: "/customer-service/bookings/void",
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: {
                booking_id: bookingId,
                reason: reason,
            },
        })
            .done((response) => {
                $bookingVoidModal.modal("hide");
                Toast.fire({
                    icon: "success",
                    title: "Voided!",
                    text: response.success || "Booking voided successfully.",
                });
                table.ajax.reload(null, false);
            })
            .fail((xhr) => {
                const errorMessage =
                    xhr.responseJSON?.error ||
                    xhr.responseJSON?.message ||
                    "Failed to void booking. Please try again.";
                Swal.fire({
                    icon: "error",
                    title: "Void Failed",
                    text: errorMessage,
                });
            })
            .always(() => {
                $("#LoadingScreen").fadeOut(200);
                setButtonLoading($submitBtn, false);
            });
    });

    $("#btn-refresh-bookings").on("click", function () {
        table.ajax.reload(null, false);
    });

    // ── Occupancy Panel ──────────────────────────────────────────────────

    const REFRESH_INTERVAL = 60; // seconds
    let occupancyData      = null;
    let serverTsDelta      = 0;  // difference: client unix - server unix at load time
    let countdownVal       = REFRESH_INTERVAL;
    let countdownTimer     = null;
    let autoRefreshTimer   = null;

    function nowTs() {
        // Returns current unix timestamp adjusted to match server time
        return Math.floor(Date.now() / 1000) - serverTsDelta;
    }

    function fmt12h(timeStr) {
        if (!timeStr) return "—";
        const [h, m] = timeStr.split(":").map(Number);
        const ampm = h >= 12 ? "PM" : "AM";
        const h12  = h % 12 || 12;
        return `${h12}:${String(m).padStart(2, "0")} ${ampm}`;
    }

    function fmtDuration(h) {
        return h === 1 ? "1 hr" : `${h} hrs`;
    }

    function minutesDiff(ts) {
        return Math.round((ts - nowTs()) / 60);
    }

    function getSlotState(booking) {
        if (!booking) return "free";
        const now    = nowTs();
        const start  = booking.start_ts;
        const end    = booking.end_ts;
        const noShow = booking.no_show_ts;

        if (now > end)    return "overdue";   // window passed, not yet completed
        if (now >= start) {
            if (booking.status === 11) return "pending";     // approved but still pending in system
            if (now >= noShow)          return "no_show";    // 15 min past, still there
            return "active";
        }
        // upcoming
        const mins = (start - now) / 60;
        return mins <= 30 ? "upcoming_soon" : "upcoming";
    }

    const STATE_STYLES = {
        free:         { border: "#6c757d", badge: '<span class="badge bg-secondary">Free</span>',                         dim: true  },
        active:       { border: "#198754", badge: '<span class="badge bg-success">Active</span>',                         dim: false },
        no_show:      { border: "#dc3545", badge: '<span class="badge bg-danger">No-Show Risk</span>',                    dim: false },
        pending:      { border: "#ffc107", badge: '<span class="badge bg-warning text-dark">Pending</span>',              dim: false },
        upcoming:     { border: "#0d6efd", badge: '<span class="badge bg-primary">Upcoming</span>',                       dim: true  },
        upcoming_soon:{ border: "#ffc107", badge: '<span class="badge bg-warning text-dark">Soon</span>',                 dim: false },
        overdue:      { border: "#adb5bd", badge: '<span class="badge" style="background:#adb5bd;">Overdue</span>',       dim: true  },
    };

    function buildSlotCard(slot) {
        const b     = slot.booking;
        const state = getSlotState(b);
        const style = STATE_STYLES[state] || STATE_STYLES.free;
        const dim   = style.dim ? "opacity:.55;" : "";

        let detail = "";
        let actions = "";

        if (b) {
            const startLabel = fmt12h(b.time);
            const endHour    = new Date(b.end_ts * 1000);
            const endLabel   = endHour.toLocaleTimeString("en-PH", { hour: "numeric", minute: "2-digit", hour12: true });
            const minsLeft   = Math.round((b.end_ts - nowTs()) / 60);
            const minsUntil  = Math.round((b.start_ts - nowTs()) / 60);

            let timeInfo = "";
            if (state === "active" || state === "no_show") {
                timeInfo = minsLeft > 0
                    ? `<div class="text-${state === "no_show" ? "danger" : "success"} small fw-semibold">${minsLeft} min left</div>`
                    : `<div class="text-muted small">Should have ended</div>`;
            } else if (state === "upcoming" || state === "upcoming_soon") {
                timeInfo = `<div class="text-primary small">In ${minsUntil} min</div>`;
            } else if (state === "overdue") {
                timeInfo = `<div class="text-muted small">Ended ${Math.abs(minsLeft)} min ago</div>`;
            } else if (state === "pending") {
                timeInfo = `<div class="text-warning small">Pending arrival</div>`;
            }

            detail = `
                <div class="mt-1 small">
                    <div class="fw-semibold">${b.name}</div>
                    <div class="text-muted">${b.people} guest(s) &bull; ${fmtDuration(b.duration_hours)}</div>
                    <div class="text-muted">${startLabel} &ndash; ${endLabel}</div>
                    ${timeInfo}
                </div>`;

            // Action buttons
            if (state === "active" || state === "upcoming_soon" || state === "pending" || state === "overdue") {
                actions += `<button class="btn btn-sm btn-success occ-complete-early mt-2" data-id="${b.id}" style="font-size:.75rem;">
                                <i class="fa-solid fa-check me-1"></i>Complete Early
                            </button>`;
            }
            if (state === "no_show") {
                actions += `<button class="btn btn-sm btn-success occ-complete-early mt-2 me-1" data-id="${b.id}" style="font-size:.75rem;">
                                <i class="fa-solid fa-check me-1"></i>Complete Early
                            </button>
                            <button class="btn btn-sm btn-danger occ-no-show mt-2" data-id="${b.id}" style="font-size:.75rem;">
                                <i class="fa-solid fa-user-slash me-1"></i>No-Show
                            </button>`;
            }
        }

        return `
            <div class="border rounded p-2" style="min-width:160px;max-width:200px;border-color:${style.border} !important;${dim}">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="fw-semibold small">Table #${slot.slot}</span>
                    ${style.badge}
                </div>
                ${detail || '<div class="text-muted small">Open</div>'}
                ${actions}
            </div>`;
    }

    function renderOccupancy(data) {
        serverTsDelta = Math.floor(Date.now() / 1000) - data.server_timestamp;
        occupancyData = data;

        const $container = $("#occupancy-container");
        $container.empty();

        if (!data.tables || data.tables.length === 0) {
            $container.html('<p class="text-muted">No active table categories found.</p>');
            return;
        }

        data.tables.forEach((cat) => {
            const slotCards = cat.slots.map(buildSlotCard).join("");
            $container.append(`
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2 border-bottom pb-1">
                        ${cat.name}
                        <small class="text-muted fw-normal">— cap. ${cat.capacity} &bull; ${cat.quantity} table(s)</small>
                    </h6>
                    <div class="d-flex flex-wrap gap-2">${slotCards}</div>
                </div>`);
        });
    }

    function loadOccupancy(silent = false) {
        if (!silent) {
            $("#occupancy-loading").show();
        }
        $.ajax({
            url: "/customer-service/bookings/live-occupancy",
            method: "GET",
        })
            .done((data) => {
                renderOccupancy(data);
            })
            .fail(() => {
                if (!silent) {
                    $("#occupancy-container").html('<p class="text-danger">Failed to load occupancy data.</p>');
                }
            })
            .always(() => {
                $("#occupancy-loading").hide();
            });
    }

    // Live clock (updates every second)
    function updateClock() {
        const now = new Date();
        $("#occupancy-clock").text(
            now.toLocaleTimeString("en-PH", { hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: true })
        );

        // Re-render cards (state recalculation) every second if data is loaded
        if (occupancyData) {
            renderOccupancy(occupancyData);
        }
    }

    // Countdown timer
    function startCountdown() {
        clearInterval(countdownTimer);
        clearTimeout(autoRefreshTimer);
        countdownVal = REFRESH_INTERVAL;
        $("#refresh-countdown").text(countdownVal);

        countdownTimer = setInterval(() => {
            countdownVal--;
            $("#refresh-countdown").text(countdownVal);
            if (countdownVal <= 0) {
                clearInterval(countdownTimer);
                loadOccupancy(true);
                startCountdown();
            }
        }, 1000);
    }

    // Occupancy action handlers
    $("#occupancy-container").on("click", ".occ-complete-early", function () {
        const bookingId = $(this).data("id");
        Swal.fire({
            title: "Complete Booking Early?",
            text: "This will mark the booking as completed and free the table immediately.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            confirmButtonText: "Yes, complete it",
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `/customer-service/bookings/complete-early/${bookingId}`,
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .done((res) => {
                    Toast.fire({ icon: "success", title: res.success || "Booking completed." });
                    loadOccupancy(true);
                    table.ajax.reload(null, false);
                    startCountdown();
                })
                .fail((xhr) => {
                    Swal.fire("Error", xhr.responseJSON?.error || "Failed to complete booking.", "error");
                });
        });
    });

    $("#occupancy-container").on("click", ".occ-no-show", function () {
        const bookingId = $(this).data("id");
        Swal.fire({
            title: "Mark as No-Show?",
            text: "The booking will be voided and the table freed. An email will be sent to the customer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, mark no-show",
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: `/customer-service/bookings/mark-no-show/${bookingId}`,
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrfToken },
            })
                .done((res) => {
                    Toast.fire({ icon: "success", title: res.success || "Marked as no-show." });
                    loadOccupancy(true);
                    table.ajax.reload(null, false);
                    startCountdown();
                })
                .fail((xhr) => {
                    Swal.fire("Error", xhr.responseJSON?.error || "Failed to mark as no-show.", "error");
                });
        });
    });

    $("#btn-refresh-occupancy").on("click", function () {
        loadOccupancy();
        startCountdown();
    });

    // Kick off
    setInterval(updateClock, 1000);
    updateClock();
    loadOccupancy();
    startCountdown();

    $bookingApproveModal.on("hidden.bs.modal", () => {
        $("#approveBookingForm")[0].reset();
        $("#approveTableSelect").empty();
        $("#approveTableAlert").addClass("d-none").text("");
        $("#approveBookingSubmit").prop("disabled", false);
    });

    $bookingRejectModal.on("hidden.bs.modal", () => {
        $("#rejectBookingForm")[0].reset();
        $("#rejectBookingSubmit").prop("disabled", false);
    });

    $bookingVoidModal.on("hidden.bs.modal", () => {
        $("#voidBookingForm")[0].reset();
        $("#voidBookingSubmit").prop("disabled", false);
    });
});
