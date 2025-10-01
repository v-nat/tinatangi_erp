$(document).ready(function () {
    function formatDate(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    }
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
            { data: "purchase_orderId", className: "dt-left" },
            { data: "type", className: "dt-left" },
            {
                data: "order_date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", className: "dt-left"
            },
            { data: "supplier_id", className: "dt-left" },
            {
                data: "expected_delivery_date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", className: "dt-left"
            },
            {
                data: "delivery_date",
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                },
                type: "date", className: "dt-left"
            },
            { data: "delivery_name", className: "dt-left" },
            { data: "created_by_id", className: "dt-left" },
            { data: "remarks", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    if (
                        row.status ==
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
                    } else if (
                        row.status ==
                        '<span class="badge bg-warning">Approved</span>'
                    ) {
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
                    } else {
                        return `
                        <div class="action-btns">
                            <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                            data-id="${data}"
                            title="View">
                                <i class="fa-solid fa-eye"></i>
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
