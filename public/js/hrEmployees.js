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
                data: 'employee_id',
                render: function(data, type, row) {
                    return `
                        <a href="#" class="action-btn btn-edit bs-tooltip me-2"
                           data-id="${data}"
                           data-name="${row.name}"
                           data-employee-id="${row.employee_id}"
                           title="Edit">
                            <i class="fa-solid fa-user-pen"></i>
                        </a>
                        `;
                }
            }
        ]
    });
    $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const mode = "edit";
        window.location.href = "/humanresources/edit-employee/" + id ;
    });

});