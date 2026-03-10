/* =========================================================
   governmentDiscountManagement.js
   Admin CRUD for government-mandated discount types
   ========================================================= */

$(function () {

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    /* ─── DataTable ─── */
    const table = $('#govdTable').DataTable({
        ajax: {
            url: '/customer-service/government-discounts/list',
            type: 'GET',
            dataSrc: 'data',
        },
        columns: [
            {
                data: null, orderable: false, searchable: false,
                title: '<input type="checkbox" id="select-all-govd">',
                render: function (_d, _t, row) {
                    return `<input type="checkbox" class="row-check-govd" data-id="${row.id}">`;
                },
            },
            { data: 'name' },
            {
                data: 'percentage',
                render: function (v) {
                    return `<span class="badge" style="background:rgba(205,164,94,.15);border:1px solid #cda45e;color:#cda45e">${v}%</span>`;
                },
            },
            {
                data: 'description',
                render: function (v) { return v || '<span class="opacity-50">—</span>'; },
            },
            {
                data: 'is_active',
                render: function (v) {
                    return v
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Inactive</span>';
                },
            },
            {
                data: null, orderable: false, searchable: false,
                render: function (_d, _t, row) {
                    return `
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary btn-toggle-govd" data-id="${row.id}" title="Toggle Active">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary btn-edit-govd" data-id="${row.id}" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete-govd" data-id="${row.id}" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>`;
                },
            },
        ],
        order: [[1, 'asc']],
        pageLength: 15,
        responsive: true,
        language: { emptyTable: 'No government discount types found.' },
    });

    /* ─── Modal ─── */
    const govModal  = new bootstrap.Modal(document.getElementById('govDiscountModal'));
    const $form     = $('#govdForm');
    const $modalLbl = $('#govDiscountModalLabel');

    function resetForm() {
        $form[0].reset();
        $('#govd_id').val('');
        $('#govd_is_active').val('1');
    }

    /* ─── Add ─── */
    $('#btn-add-govd').on('click', function () {
        resetForm();
        $modalLbl.html('<i class="fa-solid fa-id-card me-2" style="color:#cda45e"></i> Add Discount Type');
        govModal.show();
    });

    /* ─── Edit ─── */
    $('#govdTable tbody').on('click', '.btn-edit-govd', function () {
        var id = $(this).data('id');
        $.get('/customer-service/government-discounts/edit/' + id, function (data) {
            resetForm();
            $modalLbl.html('<i class="fa-solid fa-pen me-2" style="color:#cda45e"></i> Edit Discount Type');
            $('#govd_id').val(data.id);
            $('#govd_name').val(data.name);
            $('#govd_percentage').val(data.percentage);
            $('#govd_description').val(data.description || '');
            $('#govd_is_active').val(data.is_active ? '1' : '0');
            govModal.show();
        }).fail(function () {
            Swal.fire('Error', 'Failed to load discount type.', 'error');
        });
    });

    /* ─── Submit ─── */
    $form.on('submit', function (e) {
        e.preventDefault();
        var id  = $('#govd_id').val();
        var url = id
            ? '/customer-service/government-discounts/update/' + id
            : '/customer-service/government-discounts/store';

        var payload = new FormData(this);
        payload.set('_token', csrfToken);

        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            processData: false,
            contentType: false,
            success: function (res) {
                govModal.hide();
                table.ajax.reload();
                Toast.fire('Success!', res.success, 'success');
            },
            error: function (xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors;
                var msg    = errors ? Object.values(errors).flat().join('\n') : 'Something went wrong.';
                Swal.fire('Validation Error', msg, 'error');
            },
        });
    });

    /* ─── Toggle Active ─── */
    $('#govdTable tbody').on('click', '.btn-toggle-govd', function () {
        var id = $(this).data('id');
        $.ajax({
            url:  '/customer-service/government-discounts/toggle-active/' + id,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function () { table.ajax.reload(null, false); },
            error:   function () { Toast.fire('Error', 'Failed to toggle status.', 'error'); },
        });
    });

    /* ─── Delete ─── */
    $('#govdTable tbody').on('click', '.btn-delete-govd', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete this discount type?',
            text: 'Orders using this type will have the reference cleared.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url:  '/customer-service/government-discounts/destroy/' + id,
                type: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    table.ajax.reload();
                    Toast.fire('Deleted!', res.success, 'success');
                },
                error: function () { Toast.fire('Error', 'Failed to delete.', 'error'); },
            });
        });
    });

    /* ─── Batch delete ─── */
    var $batchBtn = $('#btn-batch-delete-govd');

    $(document).on('change', '.row-check-govd', function () {
        $batchBtn.toggleClass('d-none', $('.row-check-govd:checked').length === 0);
    });

    $('#select-all-govd').on('change', function () {
        $('.row-check-govd').prop('checked', this.checked);
        $batchBtn.toggleClass('d-none', !this.checked);
    });

    table.on('draw', function () {
        $('#select-all-govd').prop('checked', false);
        $batchBtn.addClass('d-none');
    });

    /* ─── Refresh ─── */
    $('#btn-refresh-govd').on('click', function () {
        table.ajax.reload(null, false);
    });

});
