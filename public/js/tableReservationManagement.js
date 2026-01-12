$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
    const viewTableModal = new bootstrap.Modal(document.getElementById('viewTableModal'));

    const $tableForm = $('#tableForm');
    const $modalTitle = $('#tableModalLabel');
    const $saveBtn = $('#saveTableBtn');

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
                render: function (data, type, row) {
                    return data == 1 ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-danger">Unavailable</span>';
                }
            },
            {
                data: "id",
                width: "15%",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
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

    $('#addTableBtn').on('click', function () {
        $tableForm[0].reset();
        $tableForm.find('.is-invalid').removeClass('is-invalid');
        $modalTitle.text('Add New Table');
        $('#table_id').val('');
        $('#form_method').val('POST');
        $('#image_preview').hide();
        $saveBtn.text('Save Table').prop('disabled', false);
        tableModal.show();
    });

    $('#tables-table').on('click', '.view-btn', function () {
        const rowData = table.row($(this).closest('tr')).data();

        const imgUrl = rowData.image ? rowData.image : 'https://placehold.co/600x400/EEE/31343C?text=No+Image';
        const statusBadge = rowData.status == 1
            ? '<span class="badge bg-success">Available</span>'
            : '<span class="badge bg-danger">Unavailable</span>';

        $('#viewModalLabel').text('Details for ' + rowData.name);
        $('#viewImage').attr('src', '/storage/app/public/' + imgUrl).attr('alt', rowData.name);
        $('#viewName').text(rowData.name);
        $('#viewLocation').text(rowData.location);
        $('#viewCapacity').text(rowData.capacity);
        $('#viewQuantity').text(rowData.quantity);
        $('#viewStatus').html(statusBadge);
        $('#viewDescription').text(rowData.description);

        viewTableModal.show();
    });

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
                $('#location').val(data.location);
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

                $saveBtn.text('Update Table').prop('disabled', false);
                tableModal.show();
            },
            error: function (xhr) {
                Swal.fire('Error', 'Failed to fetch table data.', 'error');
            }
        });
    });

    $tableForm.on('submit', function (e) {
        e.preventDefault();
        $saveBtn.prop('disabled', true).text('Saving...');
        $tableForm.find('.is-invalid').removeClass('is-invalid');

        const tableId = $('#table_id').val();
        let url = tableId
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
                        const input = $(`#${key}`);
                        input.addClass('is-invalid');
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

    $('#image').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#image_preview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#image_preview').hide();
        }
    });

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
        $("#select-all-tables").prop(
            "checked",
            totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes
        );
        updateDeleteButtonState();
    });

    table.on("draw", function () {
        $("#select-all-tables").prop("checked", false);
        updateDeleteButtonState();
    });

    $("#btn-delete-selected-tables").on("click", function () {
        const selectedIds = $(".table-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

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
                        Toast.fire(
                            "Error",
                            xhr.responseJSON.message ||
                            "Could not delete the selected tables.",
                            "error"
                        );
                    },
                });
            }
        });
    });

    $("#btn-refresh-tables").on("click", function () {
        table.ajax.reload(null, false);
    });
});
