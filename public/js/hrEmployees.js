$(document).ready(function () {
    $('#employee_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/humanresources/employees/get",
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            {
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                },
                className: 'text-center'
            },
            { data: 'name' },
            { data: 'position' },
            { data: 'department' },
            { data: 'email' },
            { data: 'direct_supervisor' },
            {
                data: 'status',
                render: function(data) {
                    return `<span class="badge bg-light-success ${data === 'active' ? 'success' : 'danger'}">${data}</span>`;
                }
            },
            {
                data: 'id',
                render: function(data, type, row) {
                    return `
                    <div class="action-btns">
                        <a href="#" class="action-btn btn-edit bs-tooltip me-2"
                           data-id="${data}"
                           data-name="${row.name}"
                           data-employee-id="${row.employee_id}"
                           title="Edit">
                            <i class="fa-solid fa-user-pen"></i>
                        </a>
                        <a href="#" class="action-btn btn-status bs-tooltip me-2"
                           data-id="${data}"
                           data-status="${row.status}"
                           data-name="${row.name}"
                           data-employee-id="${row.employee_id}"
                           title="${row.status === 'Active' ? 'Deactivate' : 'Activate'}">
                            <i class="fa-solid ${row.status === 'Active' ? 'fa-user-slash' : 'fa-user-check'}"></i>
                        </a>
                    </div>`;
                }
            }
        ]
    });
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const name = $(this).data('name');
        const employeeId = $(this).data('employee-id');

        Swal.fire({
            title: "Edit Employee Record",
            html: `<div class="text-left">
                    <p>You are about to edit the following employee:</p>
                    <ul class="list-unstyled">
                        <li><strong>Employee ID:</strong> ${employeeId}</li>
                        <li><strong>Name:</strong> ${name}</li>
                    </ul>
                    <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> Please ensure you have proper authorization to make changes.</p>
                </div>`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, Proceed to Edit",
            cancelButtonText: "Cancel",
            width: '600px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/admin/Human_Resource/edit-employees/" + id;
            }
        });
    });

});