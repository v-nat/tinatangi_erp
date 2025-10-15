$(document).ready(function () {
    const CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");
    if (CSRF_TOKEN) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": CSRF_TOKEN,
            },
        });
    }

    let lastOrderId = 0;
    const POLLING_INTERVAL = 2000;
    const ORDERS_CONTAINER = $("#ordersToday");
    const FETCH_URL = "/operations/kds/get-today-orders";
    const CHECK_URL = "/operations/kds/check-new-orders";
    const UPDATE_URL = "/operations/kds/update-status";

    function renderOrders(orders, previousMaxId) {
        ORDERS_CONTAINER.empty();

        if (!orders || orders.length === 0) {
            ORDERS_CONTAINER.html(`
                <div class="col-12 text-center my-5 p-5">
                    <h3 class="text-muted">No pending orders found.</h3>
                    <p class="text-muted">New orders will appear here automatically.</p>
                </div>
            `);
            return 0;
        }

        let maxIdAfterReload = 0;
        let cardHtml = "";

        orders.forEach((order) => {
            maxIdAfterReload = Math.max(maxIdAfterReload, order.id);

            let itemsHtml = "";
            if (Array.isArray(order.items)) {
                order.items.forEach((item) => {
                    itemsHtml += `<h6 class="text-primary mb-0 ord-item">${item.quantity}x ${item.product_name}</h6>`;
                });
            }

            const isNew = previousMaxId > 0 && order.id > previousMaxId;
            const cardClass = isNew ? "bg-light-info" : "";

            cardHtml += `
                <div class="col" data-id="${order.id}">
                    <div class="card shadow product-card-fixed-size d-flex p-2 m-2 ${cardClass}">
                        <div class="card-body p-2 flex-grow-1">
                            <h6 class="card-title mb-1 ord-id">${order.order_id}</h6>
                            <h6 class="text-primary mb-0 ord-date">${order.created_at}</h6>
                            <h6 class="text-primary mb-0 ord-type">${order.order_type}</h6>
                            <h6 class="card-title mb-1 ord-desc">Items:</h6>

                            <div class="ord-items-container">
                                ${itemsHtml}
                            </div>

                        </div>
                        <div class="footer justify-content-center">
                            <h6 class="text-primary mb-0 ord-price">₱ ${order.total_amount}</h6>
                            <span class="ord-status">${order.status}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        ORDERS_CONTAINER.append(cardHtml);

        if (previousMaxId > 0) {
            ORDERS_CONTAINER.find(`.col > .bg-light-info`).each(function () {
                const $card = $(this);
                $card.delay(5000).queue(function (next) {
                    $card.removeClass("bg-light-info");
                    next();
                });
            });
        }

        return maxIdAfterReload;
    }

    function updateOrderStatus(orderId, newStatus) {
        $.ajax({
            url: UPDATE_URL,
            type: "POST",
            dataType: "json",
            data: {
                order_id: orderId,
                new_status: newStatus,
            },
            success: function (response) {
                initialLoad();
            },
            error: function (xhr, status, error) {
                $("#LoadingScreen").fadeOut(200);
                Swal.fire(
                    "Error",
                    "Failed to update order status. Please check the console.",
                    "error"
                );
            },
        });
    }

    function initialLoad() {
        $.ajax({
            url: FETCH_URL,
            type: "GET",
            dataType: "json",
            success: function (orders) {
                lastOrderId = renderOrders(orders, 0);
            },
            error: function (xhr, status, error) {},
        });
    }

    initialLoad();

    setInterval(function () {
        const previousMaxId = lastOrderId;

        $.ajax({
            url: CHECK_URL,
            type: "GET",
            dataType: "json",
            success: function (response) {
                const newLatestId = response.latest_id;

                if (newLatestId > previousMaxId) {
                    $.ajax({
                        url: FETCH_URL,
                        type: "GET",
                        dataType: "json",
                        success: function (orders) {
                            lastOrderId = renderOrders(orders, previousMaxId);
                        },
                        error: function (xhr, status, error) {},
                    });
                } else {
                }
            },
            error: function (xhr, status, error) {},
        });
    }, POLLING_INTERVAL);

    ORDERS_CONTAINER.on("click", ".col", function () {
        const $clickedCol = $(this);
        const orderId = $clickedCol.data("id");
        const customOrderId = $clickedCol.find(".ord-id").text().trim();
        let currentStatus = $clickedCol
            .find(".ord-status")
            .text()
            .toUpperCase()
            .trim();

        let nextStatus = "";
        let text = "";
        let confirmText = "";

        if (currentStatus.includes("IN QUEUE")) {
            nextStatus = "In prep";
            text = "You are about to move this order to preparation.";
            confirmText = "Start Prep";
        } else if (currentStatus.includes("IN PREPARATION")) {
            nextStatus = "Ready";
            text = "You are about to declare this order to be ready.";
            confirmText = "Ready";
        } else if (currentStatus.includes("READY")) {
            nextStatus = "Completed";
            text =
                "You are about to mark this order as completed and remove it from the display.";
            confirmText = "Complete Order";
        } else if (currentStatus.includes("COMPLETED")) {
            return;
        } else {
            nextStatus = "In prep";
            text = "You are about to put this order in preparation.";
            confirmText = "Start Prep";
        }

        Swal.fire({
            title: "Are you sure?",
            text: text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: confirmText,
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);

            updateOrderStatus(orderId, nextStatus);

            Toast.fire({
                title:
                    nextStatus === "Completed"
                        ? "Order Finished!"
                        : "Status Updated!",
                text: `${customOrderId} is being updated to ${nextStatus}.`,
                icon: "success",
                timer: 2000,
            });
            $("#LoadingScreen").fadeOut(200);
        });
    });

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
