import $ from 'jquery';
import Swal from 'sweetalert2';

$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
    const viewTableModal = new bootstrap.Modal(document.getElementById('viewTableModal'));
    const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));

    const $tableForm = $('#tableForm');
    const $modalTitle = $('#tableModalLabel');
    const $saveBtn = $('#saveTableBtn');

    // ─── Tables DataTable ──────────────────────────────────────────────────────

    const table = $("#tables-table").DataTable({
        processing: true,
        serverSide: false,
        ajax: "/customer-service/tables/list",
        order: [[1, "asc"]],
        columns: [
            {
                data: "id",
                orderable: false,
                searchable: false,
                width: "1%",
                className: "text-center",
                title: '<input type="checkbox" class="form-check-input" id="select-all-tables">',
                render: function (data) {
                    return `<input type="checkbox" class="form-check-input table-checkbox" value="${data}">`;
                },
            },
            { data: "name" },
            { data: "location" },
            { data: "capacity", width: "10%" },
            { data: "description" },
            { data: "quantity" },
            {
                data: "status",
                width: "10%",
                render: function (data) {
                    return data == 1 ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-danger">Unavailable</span>';
                }
            },
            {
                data: "id",
                width: "15%",
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-success view-btn" data-id="${data}" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-info edit-btn" data-id="${data}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger delete-btn" data-id="${data}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                },
            },
        ],
        drawCallback: function () {
            $('#tables-table [title]').tooltip({ container: 'body' });
        }
    });

    // ─── Locations helper ──────────────────────────────────────────────────────

    function fetchAndPopulateLocations(selectedId) {
        return $.ajax({
            url: '/customer-service/table-locations/list',
            type: 'GET',
            success: function (response) {
                const locations = response.data ?? response;
                const $sel = $('#table_location_id');
                $sel.find('option:not(:first)').remove();
                locations.forEach(function (loc) {
                    $sel.append(`<option value="${loc.id}">${loc.name}</option>`);
                });
                if (selectedId) {
                    $sel.val(selectedId);
                }
            }
        });
    }

    // ─── Add Table ─────────────────────────────────────────────────────────────

    $('#addTableBtn').on('click', function () {
        $tableForm[0].reset();
        $tableForm.find('.is-invalid').removeClass('is-invalid');
        $modalTitle.text('Add New Table');
        $('#table_id').val('');
        $('#form_method').val('POST');
        $('#image_preview').hide();
        $saveBtn.text('Save Table').prop('disabled', false);
        fetchAndPopulateLocations(null);
        tableModal.show();
    });

    // ─── View Table ────────────────────────────────────────────────────────────

    $('#tables-table').on('click', '.view-btn', function () {
        const rowData = table.row($(this).closest('tr')).data();

        const imgUrl = rowData.image ? rowData.image : 'https://placehold.co/600x400/EEE/31343C?text=No+Image';
        const statusBadge = rowData.status == 1
            ? '<span class="badge bg-success">Available</span>'
            : '<span class="badge bg-danger">Unavailable</span>';

        $('#viewModalLabel').text('Details for ' + rowData.name);
        $('#viewImage').attr('src', '/storage/app/public/' + imgUrl).attr('alt', rowData.name);
        $('#viewName').text(rowData.name);
        $('#viewLocation').text(rowData.location ?? '—');
        $('#viewCapacity').text(rowData.capacity);
        $('#viewQuantity').text(rowData.quantity);
        $('#viewStatus').html(statusBadge);
        $('#viewDescription').text(rowData.description);

        viewTableModal.show();
    });

    // ─── Edit Table ────────────────────────────────────────────────────────────

    $('#tables-table').on('click', '.edit-btn', function () {
        const tableId = $(this).data('id');

        $tableForm.find('.is-invalid').removeClass('is-invalid');

        $.ajax({
            url: `/customer-service/tables/edit/${tableId}`,
            type: 'GET',
            success: function (data) {
                $modalTitle.text('Edit Table');
                $('#table_id').val(data.id);
                $('#name').val(data.name);
                $('#capacity').val(data.capacity);
                $('#quantity').val(data.quantity);
                $('#status').val(data.status);
                $('#description').val(data.description);
                $('#form_method').val('POST');

                if (data.image) {
                    $('#image_preview').attr('src', '/storage/app/public/' + data.image).show();
                } else {
                    $('#image_preview').hide();
                }

                fetchAndPopulateLocations(data.table_location_id);

                $saveBtn.text('Update Table').prop('disabled', false);
                tableModal.show();
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch table data.', 'error');
            }
        });
    });

    // ─── Submit Table Form ─────────────────────────────────────────────────────

    $tableForm.on('submit', function (e) {
        e.preventDefault();
        $saveBtn.prop('disabled', true).text('Saving...');
        $tableForm.find('.is-invalid').removeClass('is-invalid');

        const tableId = $('#table_id').val();
        const url = tableId
            ? `/customer-service/tables/update/${tableId}`
            : '/customer-service/tables/store';

        const formData = new FormData(this);

        if (tableId) {
            formData.append('_method', 'POST');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (response) {
                tableModal.hide();
                table.ajax.reload(null, false);
                Toast.fire('Success!', response.success, 'success');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}_error`).text(errors[key][0]).show();
                    }
                } else {
                    Toast.fire('Error', 'An unexpected error occurred.', 'error');
                }
            },
            complete: function () {
                $saveBtn.prop('disabled', false).text(tableId ? 'Update Table' : 'Save Table');
            }
        });
    });

    // ─── Delete Table ──────────────────────────────────────────────────────────

    $('#tables-table').on('click', '.delete-btn', function () {
        const tableId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this! The table image will also be deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer-service/tables/destroy/${tableId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (response) {
                        table.ajax.reload(null, false);
                        Toast.fire('Deleted!', response.success, 'success');
                    },
                    error: function (xhr) {
                        Toast.fire('Error', xhr.responseJSON.error || 'Failed to delete table.', 'error');
                    }
                });
            }
        });
    });

    // ─── Image preview ─────────────────────────────────────────────────────────

    $('#image').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#image_preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image_preview').hide();
        }
    });

    // ─── Bulk delete ───────────────────────────────────────────────────────────

    function updateDeleteButtonState() {
        const anyChecked = $(".table-checkbox:checked").length > 0;
        $("#btn-delete-selected-tables").toggleClass("d-none", !anyChecked);
    }

    $("#select-all-tables").on("click", function () {
        const isChecked = $(this).is(":checked");
        $(".table-checkbox").prop("checked", isChecked);
        updateDeleteButtonState();
    });

    $("#tables-table tbody").on("change", ".table-checkbox", function () {
        const totalCheckboxes = $(".table-checkbox").length;
        const checkedCheckboxes = $(".table-checkbox:checked").length;
        $("#select-all-tables").prop("checked", totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        updateDeleteButtonState();
    });

    table.on("draw", function () {
        $("#select-all-tables").prop("checked", false);
        updateDeleteButtonState();
    });

    $("#btn-delete-selected-tables").on("click", function () {
        const selectedIds = $(".table-checkbox:checked").map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            Toast.fire("No Selection", "Please select tables to delete.", "info");
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete ${selectedIds.length} tables. You won't be able to revert this!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete them!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/customer-service/tables/batch-destroy",
                    type: "DELETE",
                    data: { ids: selectedIds },
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    success: function (response) {
                        Toast.fire("Deleted!", response.success, "success");
                        table.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Toast.fire("Error", xhr.responseJSON.message || "Could not delete the selected tables.", "error");
                    },
                });
            }
        });
    });

    $("#btn-refresh-tables").on("click", function () {
        table.ajax.reload(null, false);
    });

    // ─── Locations DataTable ───────────────────────────────────────────────────

    const locationsTable = $("#locations-table").DataTable({
        processing: true,
        serverSide: false,
        ajax: "/customer-service/table-locations/list",
        order: [[0, "asc"]],
        columns: [
            { data: "name" },
            {
                data: "id",
                width: "15%",
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-info loc-edit-btn" data-id="${data}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger loc-delete-btn" data-id="${data}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                },
            },
        ],
        drawCallback: function () {
            $('#locations-table [title]').tooltip({ container: 'body' });
        }
    });

    // ─── Add Location ──────────────────────────────────────────────────────────

    $('#addLocationBtn').on('click', function () {
        $('#locationModalLabel').text('Add Location');
        $('#location_id').val('');
        $('#location_name').val('').removeClass('is-invalid');
        $('#location_name_error').text('');
        locationModal.show();
    });

    // ─── Edit Location ─────────────────────────────────────────────────────────

    $('#locations-table').on('click', '.loc-edit-btn', function () {
        const locId = $(this).data('id');

        $.ajax({
            url: `/customer-service/table-locations/edit/${locId}`,
            type: 'GET',
            success: function (data) {
                $('#locationModalLabel').text('Edit Location');
                $('#location_id').val(data.id);
                $('#location_name').val(data.name).removeClass('is-invalid');
                $('#location_name_error').text('');
                locationModal.show();
            },
            error: function () {
                Toast.fire('Error', 'Failed to fetch location data.', 'error');
            }
        });
    });

    // ─── Save Location ─────────────────────────────────────────────────────────

    $('#saveLocationBtn').on('click', function () {
        const locId = $('#location_id').val();
        const name = $('#location_name').val().trim();
        const $btn = $(this);

        $('#location_name').removeClass('is-invalid');
        $('#location_name_error').text('');

        if (!name) {
            $('#location_name').addClass('is-invalid');
            $('#location_name_error').text('Location name is required.');
            return;
        }

        $btn.prop('disabled', true).text('Saving...');

        const url = locId
            ? `/customer-service/table-locations/update/${locId}`
            : '/customer-service/table-locations/store';

        $.ajax({
            url: url,
            type: 'POST',
            data: { name: name, _token: csrfToken },
            success: function (response) {
                locationModal.hide();
                locationsTable.ajax.reload(null, false);
                Toast.fire('Success!', locId ? 'Location updated.' : 'Location added.', 'success');
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    if (errors && errors.name) {
                        $('#location_name').addClass('is-invalid');
                        $('#location_name_error').text(errors.name[0]);
                    }
                } else {
                    Toast.fire('Error', 'An unexpected error occurred.', 'error');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).text('Save');
            }
        });
    });

    // ─── Delete Location ───────────────────────────────────────────────────────

    $('#locations-table').on('click', '.loc-delete-btn', function () {
        const locId = $(this).data('id');
        const rowData = locationsTable.row($(this).closest('tr')).data();

        Swal.fire({
            title: 'Delete location?',
            text: `"${rowData.name}" will be permanently removed.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer-service/table-locations/destroy/${locId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function () {
                        locationsTable.ajax.reload(null, false);
                        Toast.fire('Deleted!', 'Location removed.', 'success');
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to delete location.';
                        Toast.fire('Error', msg, 'error');
                    }
                });
            }
        });
    });

    // ─── Refresh Locations ─────────────────────────────────────────────────────

    $("#btn-refresh-locations").on("click", function () {
        locationsTable.ajax.reload(null, false);
    });
});
