$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const faqModal = new bootstrap.Modal(document.getElementById('faqModal'));
    const $faqForm = $('#faqForm');
    const $modalTitle = $('#faqModalLabel');
    const $btnSave = $('#btn-save-faq');

    const faqTable = $('#faqsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/customer-service/faqs/list",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            { data: "order", width: "5%" },
            { data: "question", width: "30%" },
            {
                data: "answer",
                width: "40%",
                render: function (data) {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            {
                data: "status_html", // Use the accessor
                width: "10%"
            },
            {
                data: "id",
                width: "15%",
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm btn-edit" data-id="${data}">
                            <i class="fa-solid fa-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${data}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'asc']]
    });

    $('#btn-add-faq').on('click', function () {
        $faqForm[0].reset();
        $modalTitle.text('Add New FAQ');
        $faqForm.attr('action', '/customer-service/faqs/store');
        $('#faq_method').val('POST');
        $('#faq_id').val('');
        $('#status').val('34'); // Default to Hidden
        $btnSave.text('Save FAQ').prop('disabled', false);
        faqModal.show();
    });

    $('#faqsTable tbody').on('click', '.btn-edit', function () {
        const faqId = $(this).data('id');

        $.get(`/customer-service/faqs/edit/${faqId}`, function (data) {
            $modalTitle.text('Edit FAQ');
            $faqForm.attr('action', `/customer-service/faqs/update/${faqId}`);
            $('#faq_method').val('POST');
            $('#faq_id').val(data.id);
            $('#question').val(data.question);
            $('#answer').val(data.answer);
            $('#status').val(data.status); // This will be 34 or 35
            $('#order').val(data.order);
            $btnSave.text('Update FAQ').prop('disabled', false);
            faqModal.show();
        }).fail(function () {
            Swal.fire('Error', 'Could not retrieve FAQ data.', 'error');
        });
    });

    $faqForm.on('submit', function (e) {
        e.preventDefault();
        const url = $(this).attr('action');
        let formData = $(this).serialize();

        $btnSave.text('Saving...').prop('disabled', true);

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (response) {
                faqModal.hide();
                Swal.fire('Success!', response.success, 'success');
                faqTable.ajax.reload();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = 'Please check your input:<br>';
                    $.each(errors, function (key, value) {
                        errorMsg += `- ${value[0]}<br>`;
                    });
                    Swal.fire('Error', errorMsg, 'error');
                } else {
                    Swal.fire('Error', 'An unknown error occurred.', 'error');
                }
                $btnSave.text('Save FAQ').prop('disabled', false);
            }
        });
    });

    $('#faqsTable tbody').on('click', '.btn-delete', function () {
        const faqId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/customer-service/faqs/destroy/${faqId}`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function (response) {
                        Swal.fire('Deleted!', response.success, 'success');
                        faqTable.ajax.reload();
                    },
                    error: function (xhr) {
                        Swal.fire('Error', 'Could not delete the FAQ.', 'error');
                    }
                });
            }
        });
    });
});
