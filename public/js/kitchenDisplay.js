$(document).ready(function () {
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
});

$(document).ready(function () {
    // --- 1. DATATABLES INITIALIZATION ---

    const ordersTable = $("#kdsOrders").DataTable({
        destroy: true,
        paging: false,
        ordering: false,
        info: false,
        searching: false,

        ajax: {
            url: "/operations/kds/get-today-orders",
            dataSrc: "",
        },
        columns: [
            { title: "Order #", data: "order_id" },
            { title: "Cashier", data: "cashier_name" },
            { title: "Type", data: "order_type" },
            { title: "Total", data: "total_amount" },
            { title: "Time", data: "created_at" },
            {
                title: "Items",
                data: "items",
                render: function (data, type, row) {
                    if (type === "display" && Array.isArray(data)) {
                        let html = '';
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

    // ----------------------------------------------------
    // 2. LARAVEL ECHO / PUSHER REAL-TIME LISTENER
    // ----------------------------------------------------

    if (window.Echo) {
        window.Echo.channel("pos-orders").listen(".order.created", (event) => {
            console.log("KDS: New Order Received:", event);

            ordersTable.row.add(event).draw(false);

            const lastRow = ordersTable.row(":last").node();
            if (lastRow) {
                $(lastRow).addClass("table-success").delay(1000).queue(function (next) {
                    $(this).removeClass("table-success");
                    next();
                });
            }
        });
    }

    // ----------------------------------------------------
    // 3. ACTION BUTTON HANDLER (Existing logic)
    // ----------------------------------------------------
    $("#kdsOrders tbody").on("click", ".mark-ready", function () {
        // ... (Your status update logic) ...
    });

});
