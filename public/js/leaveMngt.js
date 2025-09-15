$(document).ready(function () {
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }
    
    $("#leaves_table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/humanresources/leaves/get",
            type: "GET",
            dataSrc: "data",
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
            },
            { data: "employee" },
            { data: "department" },
            { data: "position" },
            {
                data: "start_date",
                className: "text-center",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
            },
            {
                data: "end_date",
                className: "text-center",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", // Ensure proper date sorting
            },
            
            {
                data: "reason"
            },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "leave_id",
                render: function (data, type, row) {
                    return `
                    <div>
                        <div>
                        <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 approve-btn"
                           data-id="${data}"
                           data-name="${row.name}"
                           data-employee-id="${row.overtime_id}"
                           title="Approve">
                            <i class="fa-solid fa-check"></i>
                        </a>
                        <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                           data-id="${data}"
                           data-name="${row.name}"
                           data-employee-id="${row.overtime_id}"
                           title="Reject">
                            <i class="fa-solid fa-x"></i>
                        </a>
                    </div>
                       
                    </div>
                        `;
                },
            },
        ],
    });
    $(document).on("click", ".approve-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const mode = "edit";
        $('#ApprovalConfirmation').modal('show');
    });
    $(document).on("click", ".reject-btn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        const mode = "edit";
        $('#RejectionConfirmation').modal('show');
    });
});
