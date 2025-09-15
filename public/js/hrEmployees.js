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
                    <div>
                        <a href="#" class="action-btn btn-edit bs-tooltip me-2"
                           data-id="${data}"
                           data-name="${row.name}"
                           data-employee-id="${row.employee_id}"
                           title="Edit">
                            <i class="fa-solid fa-user-pen"></i>
                        </a>
                        <a href="#" class="action-btn generate-payroll bs-tooltip"
                           title="Generate Payroll"
                           data-employee-id="${row.id}"
                           data-name="${row.name}"
                           data-employee-id-number="${row.employee_id}">
                            <i class="fa-solid fa-file-invoice"></i>
                        </a>
                    </div>
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