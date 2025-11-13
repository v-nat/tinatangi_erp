$(document).ready(function () {
    const allItems = $("#allInventoryItems").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/inventory/get-all-items",
            type: "GET",
            dataSrc: "data",
        },
        order: [[2, "asc"]],
        columns: [
            {
                data: null,
                className: "text-center",
                width: "45px",
                orderable: false,
            },
            { data: "sku", className: "dt-left" },
            { data: "item_name", className: "dt-left" },
            { data: "unit", className: "dt-left" },
            { data: "category", className: "dt-left" },
            {
                data: "stock_level",
                className: "dt-left",
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        if (row.stock_display) {
                            return row.stock_display;
                        }
                        const formatted =
                            row.stock_level_formatted ??
                            Number(data || 0).toLocaleString("en-PH", {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            });
                        const unitLabel = row.unit ? ` ${row.unit}` : "";
                        return `${formatted}${unitLabel}`.trim();
                    }
                    return data;
                },
            },
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
        drawCallback: function (settings) {
            const api = this.api();
            const info = api.page.info();
            api.column(0, { page: "current", order: "current" })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = info.start + i + 1;
                });
        },
        initComplete: function () {
            const column = this.api().column(5);
            const select = $("#category_filter");

            column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        select.append(
                            $("<option></option>").attr("value", d).text(d)
                        );
                    }
                });

            const statusColumn = this.api().column(8);
            const statusSelect = $("#status_filter");
            const statusSet = new Set();

            statusColumn.data().each(function (d) {
                if (d && typeof d === "object" && d.label) {
                    statusSet.add(d.label.trim());
                } else if (typeof d === "string") {
                    const textValue = $("<div>").html(d).text().trim();
                    if (textValue) {
                        statusSet.add(textValue);
                    }
                }
            });

            Array.from(statusSet)
                .sort((a, b) => a.localeCompare(b))
                .forEach(function (label) {
                    statusSelect.append(
                        $("<option></option>").attr("value", label).text(label)
                    );
                });
        },
    });
    $("#category_filter").on("change", function () {
        const selectedCategory = $(this).val();

        allItems
            .column(5)
            .search(
                selectedCategory ? "^" + selectedCategory + "$" : "",
                true,
                false
            )
            .draw();
    });

    $("#status_filter").on("change", function () {
        const selectedStatus = $(this).val();
        const statusColumn = allItems.column(8);

        statusColumn
            .search(
                selectedStatus ? "^" + selectedStatus + "$" : "",
                true,
                false
            )
            .draw();
    });

    $("#btn-refresh-all-items").on("click", function () {
        allItems.ajax.reload(null, false);
    });
});
