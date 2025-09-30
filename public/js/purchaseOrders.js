$(document).ready(function () {
    $("#purchaseOrderTable").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/procurement/purchases/get-list",
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
                width: "50px",
            },
            { data: "type" },
            { data: "department" },
            {
                data: "amount",
                render: function (data, type, row) {
                    return (
                        "₱ " +
                        parseFloat(data).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        })
                    );
                },
            },
            { data: "requested_by_id" },
            { data: "requested_date" },
            { data: "released_by_id" },
            { data: "released_date" },
            { data: "remarks" },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (
                        row.status !==
                        '<span class="badge bg-warning">Pending</span>'
                    ) {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                        `;
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 process-btn"
                                data-id="${data}"
                                title="Process">
                                    <i class="fa-solid fa-check"></i>
                            </a>
                            <a href="#" class="btn icon btn-sm btn-danger bs-tooltip me-2 reject-btn"
                                data-id="${data}"
                                title="Reject">
                                    <i class="fa-solid fa-x"></i>
                            </a>
                        </div>
                        `;
                    }
                },
                className: "text-center",
            },
        ],
    });

});