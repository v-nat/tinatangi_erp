import { reloadTable } from "./utils/reloadTable.js";
import { formatToManilaTime } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    const transactionTypeMap = {
        IN: "Incoming Stocks",
        ADJ: "Stock Adjustment",
        OUT: "Outgoing Stocks",
    };
    const allTransactions = $("#allTransactions").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/inventory/stock-transactions/list",
            type: "GET",
            dataSrc: "data",
        },
        dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rtip',
        buttons: [
            {
                extend: "print",
                title: "",
                text: '<i class="fa-solid fa-print"></i> Print',
                className: "btn btn-sm btn-primary",
                exportOptions: {
                    columns: ":visible:not(:eq(0)):not(:eq(-1))",
                },
                customize: function (win) {
                    win.document.title =
                        "https://tinatangi.site/inventory/stock-transactions/";

                    $(win.document.head).append(
                        "<style>" +
                            "body { font-family: Arial, sans-serif; font-size: 8pt; }" +
                            ".print-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #ccc; }" +
                            ".print-header-left { font-weight: bold; }" +
                            ".print-header-right { font-size: 8pt; color: #555; }" +
                            ".print-title { text-align: center; font-size: 12pt; font-weight: bold; margin-bottom: 5px; }" +
                            ".print-subtitle { text-align: center; font-size: 10pt; font-weight: normal; margin-bottom: 20px; }" +
                            "table { width: 100%; margin-top: 20px; border: 2px solid #000; }" +
                            "table th, table td { text-align: left; padding: 4px; border: 2px solid #000; }" +
                            "</style>"
                    );

                    $(win.document.body).prepend(
                        '<div class="print-header">' +
                            '<div class="print-header-left">Tinatangi Cafe ERP Management System</div>' +
                            '<div class="print-header-right">' +
                            new Date().toLocaleString("en-US", {
                                year: "numeric",
                                month: "long",
                                day: "numeric",
                                hour: "2-digit",
                                minute: "2-digit",
                            }) +
                            "</div>" +
                            "</div>" +
                            '<div class="print-title">Tinatangi Cafe | Inventory Department</div>' +
                            '<div class="print-subtitle">Stocks Transaction Records</div>'
                    );

                    $(win.document.body).find("h1").remove();
                    $(win.document.body)
                        .find("table")
                        .removeClass("dataTable")
                        .addClass("compact")
                        .css("font-size", "inherit");
                },
            },
        ],
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
                    console.log("Final raw data received by browser:", data);
                    return formatToManilaTime(data);
                },
            },
            { data: "reference", className: "dt-left", width: "14%" },
            {
                data: "quantity",
                className: "dt-left",
                width: "8%",
                render: function (data, type, row) {
                    const quantityValue =
                        typeof row.quantity === "number"
                            ? row.quantity
                            : Number(data || 0);
                    const formatted =
                        row.quantity_formatted ??
                        Number(quantityValue || 0).toLocaleString("en-PH", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        });
                    const withUnit = row.unit
                        ? `${formatted} ${row.unit}`
                        : formatted;

                    if (type === "display" || type === "filter") {
                        return withUnit;
                    }

                    return quantityValue;
                },
            },
            { data: "item", className: "dt-left", width: "22%" },
            { data: "receive", className: "dt-left", width: "22%" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
        ],
        initComplete: function () {
            const column = this.api().column(1);
            const select = $("#transaction_type_filter");

            column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        const displayText = transactionTypeMap[d] || d;
                        select.append(
                            $("<option></option>")
                                .attr("value", d)
                                .text(displayText)
                        );
                    }
                });
            const refColumn = this.api().column(4);
            const refSelect = $("#reference_filter");

            refColumn
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        refSelect.append(
                            $("<option></option>").attr("value", d).text(d)
                        );
                    }
                });
        },
    });

    $("#transaction_type_filter").on("change", function () {
        const selectedType = $(this).val();

        allTransactions
            .column(1)
            .search(selectedType ? "^" + selectedType + "$" : "", true, false)
            .draw();
    });

    $("#batch_filter").on("keyup input", function () {
        allTransactions.column(2).search($(this).val(), false, true).draw();
    });

    $("#date_filter").on("change", function () {
        allTransactions.column(3).search($(this).val(), false, true).draw();
    });

    $("#reference_filter").on("change", function () {
        const selectedRef = $(this).val();
        allTransactions
            .column(4)
            .search(selectedRef ? "^" + selectedRef + "$" : "", true, false)
            .draw();
    });

    $("#btn-refresh-stock-transactions").on("click", function () {
        allTransactions.ajax.reload(null, false);
    });
});
