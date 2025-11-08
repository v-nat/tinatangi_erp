import { formatDate, formatTime, formatToManilaTime } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const table = $("#bookings-table").DataTable({
        processing: true,
        serverSide: false,
        ajax: "/customer-service/bookings/all",
        order: [[0, "desc"]],
        columns: [
            { data: "id", width: "5%" },
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
                render: function (data, type, row) {
                    return data ? formatDate(data) : "N/A";
                },
            },
            {
                data: "time",
                render: function (data, type, row) {
                    return data ? formatTime(data) : "N/A";
                },

            },
            { data: "people", width: "5%" },
            {
                data: "table_for_reservation.name",
                render: function (data, type, row) {
                    return data ? data : '<span class="text-muted">N/A</span>';
                },
            },
            {
                data: "status",
                className: "dt-left",
                render: function (data, type, row) {
                    if (row.status_rs && row.status_rs.status) {
                        let statuses = {
                            11: 'Pending',
                            12: 'Rejected',
                            13: 'Approved',
                            23: 'Completed',
                            31: 'Voided'
                        };

                        let options = '';
                        for (const id in statuses) {
                            options += `<option value="${id}" ${id == row.status ? 'selected' : ''}>${statuses[id]}</option>`;
                        }

                        return `
                            <select class="form-select form-select-sm status-dropdown" data-id="${row.id}">
                                ${options}
                            </select>
                        `;
                    }
                    return row.status; // Fallback
                }
            },
            {
                data: "created_at",
                render: function (data, type, row) {
                    return formatToManilaTime(data);
                },
                className: "dt-left",
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${row.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                },
            },
        ],
    });

    $("#bookings-table").on("change", ".status-dropdown", function () {
        const bookingId = $(this).data("id");
        const statusId = $(this).val();

        $.ajax({
            url: "/customer-service/bookings/update-status",
            type: "POST",
            headers: { "X-CSRF-TOKEN": csrfToken },
            data: {
                booking_id: bookingId,
                status_id: statusId,
            },
            success: function (response) {
                Toast.fire({
                    icon: "success",
                    title: "Updated!",
                    text: response.success,
                });
                table.ajax.reload(null, false); 
            },
            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Failed to update status. Please try again.",
                });
            },
        });
    });

    $("#bookings-table").on("click", ".delete-btn", function () {
        const bookingId = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer-service/bookings/delete/${bookingId}`,
                    type: "DELETE",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    success: function (response) {
                        Toast.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: response.success,
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Toast.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Failed to delete booking. Please try again.",
                        });
                    },
                });
            }
        });
    });
});
