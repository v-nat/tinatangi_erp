$(document).ready(function () {
    let lastOrderId = 0;
    const POLLING_INTERVAL = 1500;

    // -----------------------------------------------------------------
    // 0. EXIT LOGIC
    // -----------------------------------------------------------------
    $(document).on("click", "#exit-pos-btn", function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "You are about End KDS Session.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Exit",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);
            window.location.href = "/operations";
        });
    });

    // -----------------------------------------------------------------
    // 1. DATATABLES INITIALIZATION
    // -----------------------------------------------------------------
    const ordersTable = $("#kdsOrders").DataTable({
        destroy: true,
        paging: false,
        ordering: false,
        info: false,
        searching: false,
        ajax: {
            url: "/operations/kds/get-today-orders",
            dataSrc: "",
            complete: function (xhr, status) {
                if (status === "success" && xhr.responseJSON && xhr.responseJSON.length > 0) {
                    const maxId = xhr.responseJSON.reduce(
                        (max, order) => Math.max(max, order.id),
                        0
                    );
                    lastOrderId = maxId;
                }
            },
        },
        columns: [
            { title: "Order #", data: "order_id" },
            { title: "Cashier", data: "cashier_name" },
            { title: "Type", data: "order_type" },
            { title: "Total", data: "total_amount", className: "dt-left" },
            { title: "Time", data: "created_at" },
            {
                title: "Items",
                data: "items",
                render: function (data, type, row) {
                    if (type === "display" && Array.isArray(data)) {
                        let html = "";
                        data.forEach((item) => {
                            html += `<div class="p-1 border-bottom"><strong>${item.quantity}x</strong> ${item.product_name}</div>`;
                        });
                        return html;
                    }
                    return "No Items";
                },
            },
            { title: "Status", data: "status", className: "font-weight-bold" },
            {
                title: "Action",
                data: null,
                defaultContent: '<button class="btn btn-sm btn-primary mark-ready">Ready</button>',
                orderable: false,
            },
        ],
    });

    // -----------------------------------------------------------------
    // 2. SMART POLLING LOGIC (The efficient real-time solution)
    // -----------------------------------------------------------------
    setInterval(function () {
        $.ajax({
            url: "/operations/kds/check-new-orders",
            type: "GET",
            dataType: "json",
            success: function (response) {
                const newLatestId = response.latest_id;
                const previousMaxId = lastOrderId;

                if (newLatestId > previousMaxId) {
                    console.log(
                        `NEW ORDER DETECTED! Previous ID: ${previousMaxId}, New ID: ${newLatestId}. Reloading table...`
                    );
                    ordersTable.ajax.reload(function (json) {
                        if (json && json.length > 0) {
                            const maxIdAfterReload = json.reduce(
                                (max, order) => Math.max(max, order.id),
                                0
                            );
                            lastOrderId = maxIdAfterReload;
                        }

                        ordersTable.rows().every(function () {
                            const rowData = this.data();

                            if (rowData.id > previousMaxId) {
                                const rowNode = this.node();
                                $(rowNode)
                                    .addClass('table-success')
                                    .delay(5000)
                                    .queue(function(next) {
                                        $(this).removeClass('table-success');
                                        next();
                                    });

                                console.log(`Highlighted new order: ${rowData.order_id}`);
                            }
                        });

                    }, false);
                } else {
                }
            },
            error: function (xhr, status, error) {
                console.error("KDS Polling Error:", error);
            },
        });
    }, POLLING_INTERVAL);

    // ----------------------------------------------------
    // 3. ACTION BUTTON HANDLER
    // ----------------------------------------------------
    $("#kdsOrders tbody").on("click", ".mark-ready", function () {
        const row = ordersTable.row($(this).parents("tr"));
        const orderData = row.data();

        // ... Your AJAX logic to update order status goes here ...
    });
});
