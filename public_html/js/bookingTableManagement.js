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
