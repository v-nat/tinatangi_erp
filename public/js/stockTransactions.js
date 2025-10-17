import { reloadTable } from "./utils/reloadTable.js";
import { formatToManilaTime } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    $("#allTransactions").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/inventory/stock-transactions/list",
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
            {
                data: "type",
                className: "dt-left",
                width: "16%",
                render: function (data, type, row) {
                    if (type === "display") {
                        switch (data) {
                            case "IN":
                                return "Incoming Stocks";
                            case "ADJ":
                                return "Stock Adjustment";
                            case "OUT":
                                return "Outgoing Stocks";
                            default:
                                return data;
                        }
                    }
                    return data;
                },
            },
            { data: "batch", className: "dt-left", width: "8%" },
            {
                data: "date",
                className: "dt-left",
                width: "20%",
                render: function (data) {
                    return data ? formatToManilaTime(data) : "N/A";
                },
                type: "date",
            },
            { data: "reference", className: "dt-left", width: "14%" },
            { data: "quantity", className: "dt-left", width: "8%" },
            { data: "item", className: "dt-left", width: "22%" },
            { data: "receive", className: "dt-left", width: "22%" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
        ],
    });
});
