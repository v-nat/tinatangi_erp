import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    $("#allInventoryItems").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/inventory/get-all-items",
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
                width: "45px",
            },
            { data: "sku", className: "dt-left" },
            { data: "item_name", className: "dt-left" },
            { data: "unit", className: "dt-left" },
            { data: "inventory_location", className: "dt-left" },
            { data: "category", className: "dt-left" },
            { data: "stock_level", className: "dt-left" },
            {
                data: "cost_price",
                className: "dt-left",
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
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
        ],
    });
});
