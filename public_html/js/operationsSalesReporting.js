$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");
    if (csrfToken) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
        });
    }

    const $dateInput = $("#reporting_date");
    const $submitBtn = $("#submit-sales-report");
    const $selectAll = $("#select-all-orders");
    const $submittedCount = $("#submitted-count");

    const ordersTable = $("#orders-for-reporting").DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false,
        ajax: {
            url: "/operations/sales/reporting/orders",
            data: function (d) {
                d.date = $dateInput.val();
            },
            dataSrc: "data",
        },
        order: [[4, "desc"]],
        columnDefs: [
            { targets: [0, 5], orderable: false, searchable: false },
        ],
        columns: [
            {
                data: "id",
                render: function (data, type, row) {
                    return `<input type="checkbox" class="form-check-input order-checkbox" value="${data}">`;
                },
                className: "text-center",
                width: "45px",
            },
            { data: "order_code" },
            { data: "order_type" },
            {
                data: "total_amount",
                render: function (data) {
                    return "₱ " + parseFloat(data).toFixed(2);
                },
            },
            { data: "created_at" },
            {
                data: "items",
                render: function (items) {
                    if (!Array.isArray(items) || items.length === 0) {
                        return '<span class="text-muted">No items</span>';
                    }
                    return `
                        <ul class="mb-0 ps-3">
                            ${items
                                .map(
                                    (item) =>
                                        `<li>${item.quantity}x ${item.name}</li>`
                                )
                                .join("")}
                        </ul>
                    `;
                },
            },
        ],
        drawCallback: function () {
            $selectAll.prop("checked", false);
            toggleSubmitButton();
        },
    });

    const submittedTable = $("#submitted-reports").DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false,
        ajax: {
            url: "/operations/sales/reporting/submissions",
            data: function (d) {
                d.date = $dateInput.val();
            },
            dataSrc: function (json) {
                $submittedCount.text(`${json.data.length} reports today`);
                return json.data;
            },
        },
        order: [[3, "desc"]],
        columns: [
            { data: "order_code" },
            {
                data: "total_amount",
                render: function (data) {
                    return "₱ " + parseFloat(data).toFixed(2);
                },
            },
            {
                data: "status_html",
                render: function (data) {
                    return data || '<span class="badge bg-secondary">Unknown</span>';
                },
                className: "text-center",
            },
            { data: "reported_at" },
            { data: "reviewed_at" },
            { data: "reviewed_by" },
        ],
    });

    function reloadTables() {
        ordersTable.ajax.reload();
        submittedTable.ajax.reload();
    }

    function toggleSubmitButton() {
        const selectedCount = $(".order-checkbox:checked").length;
        $submitBtn.prop("disabled", selectedCount === 0);
    }

    $("#refresh-orders").on("click", function () {
        reloadTables();
    });

    $("#btn-refresh-submitted-reports").on("click", function () {
        submittedTable.ajax.reload(null, false);
    });

    $dateInput.on("change", function () {
        reloadTables();
    });

    $("#orders-for-reporting tbody").on("change", ".order-checkbox", function () {
        const totalCheckboxes = $(".order-checkbox").length;
        const checkedCheckboxes = $(".order-checkbox:checked").length;

        if (totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0) {
            $selectAll.prop("checked", true);
        } else {
            $selectAll.prop("checked", false);
        }

        toggleSubmitButton();
    });

    $selectAll.on("change", function () {
        const isChecked = $(this).is(":checked");
        $(".order-checkbox").prop("checked", isChecked);
        toggleSubmitButton();
    });

    $submitBtn.on("click", function () {
        const selectedOrderIds = $(".order-checkbox:checked")
            .map(function () {
                return $(this).val();
            })
            .get();

        if (selectedOrderIds.length === 0) {
            Toast.fire({
                icon: "warning",
                title: "No orders selected",
                text: "Please choose at least one order to submit.",
            });
            return;
        }

        $submitBtn.prop("disabled", true);
        $("#LoadingScreen").fadeIn(200);

        $.ajax({
            url: "/operations/sales/reporting/submit",
            type: "POST",
            data: {
                order_ids: selectedOrderIds,
            },
            success: function (response) {
                Toast.fire({
                    icon: "success",
                    title: "Reported!",
                    text: response.message,
                });
                reloadTables();
            },
            error: function (xhr) {
                const message =
                    xhr.responseJSON?.message ||
                    "Failed to submit sales report.";
                Toast.fire({
                    icon: "error",
                    title: "Error",
                    text: message,
                });
            },
            complete: function () {
                $("#LoadingScreen").fadeOut(200);
                $submitBtn.prop("disabled", true);
            },
        });
    });
});

