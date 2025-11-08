$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    // 1. Add instances for BOTH modals
    const tableModal = new bootstrap.Modal(document.getElementById('tableModal'));
    const viewTableModal = new bootstrap.Modal(document.getElementById('viewTableModal')); // New

    const $tableForm = $('#tableForm');
    const $modalTitle = $('#tableModalLabel');
    const $saveBtn = $('#saveTableBtn');

    const table = $("#tables-table").DataTable({
        processing: true,
        serverSide: false,
        ajax: "/customer-service/tables/list",
        order: [[0, "asc"]],
        columns: [
            { data: "id", width: "5%" },

            // 2. The 'image' column has been REMOVED

            { data: "name" },
            { data: "capacity", width: "10%" },
            {
                data: "status",
                width: "10%",
                render: function (data, type, row) {
                    return data == 1 ? '<span class="badge bg-success">Available</span>' : '<span class="badge bg-danger">Unavailable</span>';
                }
            },
            { data: "description" },
            {
                data: null,
                width: "15%", // Adjusted width to fit the new button
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    // 3. Add the 'View' button to the actions column
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-success view-btn" data-id="${row.id}" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-info edit-btn" data-id="${row.id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger delete-btn" data-id="${row.id}" title="Delete">
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

    // 4. Add the new click handler for the .view-btn
    $('#tables-table').on('click', '.view-btn', function () {
        const rowData = table.row($(this).closest('tr')).data();

        const imgUrl = '/storage/app/public/' + rowData.image ? rowData.image : 'https://placehold.co/600x400/EEE/31343C?text=No+Image';
        const statusBadge = rowData.status == 1
            ? '<span class="badge bg-success">Available</span>'
            : '<span class="badge bg-danger">Unavailable</span>';

        $('#viewModalLabel').text('Details for ' + rowData.name);
        $('#viewImage').attr('src', imgUrl).attr('alt', rowData.name);
        $('#viewName').text(rowData.name);
        $('#viewCapacity').text(rowData.capacity);
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
                $('#capacity').val(data.capacity);
                $('#status').val(data.status);
                $('#description').val(data.description);
                $('#form_method').val('POST');

                if (data.image) {
                    $('#image_preview').attr('src', data.image).show();
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
                Swal.fire('Success!', response.success, 'success');
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
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
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
                        Swal.fire('Deleted!', response.success, 'success');
                    },
                    error: function (xhr) {
                        Swal.fire('Error', xhr.responseJSON.error || 'Failed to delete table.', 'error');
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
});
