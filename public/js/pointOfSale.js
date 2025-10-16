$(document).ready(function () {
    $(document).on("click", "#exit-pos-btn", function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "You are about End POS Session.",
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

    function getProducts(url, id) {
        $.get(url, function (response) {
            if (response.data && Array.isArray(response.data)) {
                const productsArray = response.data;
                let productsHtml = [];

                productsArray.forEach((element) => {
                    const imagePath = element.image;

                    let imageUrl;

                    if (imagePath && imagePath !== "N/A") {
                        imageUrl = "img/products/" + imagePath;
                    } else {
                        imageUrl = DEFAULT_PRODUCT_IMAGE;
                    }

                    productsHtml.push(`
                        <div class="col"  data-id="${element.id}">
                            <div class="card shadow h-100 product-card-fixed-size d-flex p-2 m-2">
                                <img src="${imageUrl}"
                                    class="card-img-top img-fluid prod-img" alt="Product Image">
                                <div class="card-body p-2 flex-grow-1">
                                    <h6 class="card-title mb-1 prod-name">${
                                        element.name
                                    }</h6>
                                    <h6 class="text-success mb-0 prod-price">₱${parseFloat(
                                        element.base_price || 0
                                    ).toFixed(2)}</h6>
                                </div>
                            </div>
                        </div>
                    `);
                });

                $(id).html(productsHtml.join(""));
            } else {
                console.error(
                    "Error: products data not found or is not an array."
                );
                alert("Error: products not found.");
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load products.";
                console.error("AJAX Error:", errorMsg, xhr);
                alert(errorMsg);
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    }

    function getAllProducts() {
        getProducts(`/operations/pos/get-all-products`, "#allProducts");
    }

    function getPastriesProducts() {
        getProducts(
            `/operations/pos/get-pastries-products`,
            "#pastriesProducts"
        );
    }

    function getBeveragesProducts() {
        getProducts(
            `/operations/pos/get-beverages-products`,
            "#beveragesProducts"
        );
    }

    function getMealsProducts() {
        getProducts(`/operations/pos/get-meals-products`, "#mealsProducts");
    }

    function getSnacksAndSidesProducts() {
        getProducts(
            `/operations/pos/get-snacks-sides-products`,
            "#snacksProducts"
        );
    }

    getAllProducts();

    $(document).on("shown.bs.tab", "#v-pills-all-tab", function (e) {
        getAllProducts();
    });
    $(document).on("shown.bs.tab", "#v-pills-pastries-tab", function (e) {
        getPastriesProducts();
    });
    $(document).on("shown.bs.tab", "#v-pills-beverages-tab", function (e) {
        getBeveragesProducts();
    });
    $(document).on("shown.bs.tab", "#v-pills-meals-tab", function (e) {
        getMealsProducts();
    });
    $(document).on("shown.bs.tab", "#v-pills-snacks-tab", function (e) {
        getSnacksAndSidesProducts();
    });

    $(document).on("click", ".col", function () {
        const clickedColumn = $(this);
        const id = clickedColumn.data("id");
        const productNameElement = clickedColumn.find(".prod-name");
        const productName = productNameElement.text().trim();
        const priceElement = clickedColumn.find(".prod-price");
        const priceText = priceElement.text().trim();
        const price = parseFloat(priceText.replace("₱", "").trim());

        $("#_item_id").val(id);
        $("#_item_name").text("Product Name: " + productName);
        $("#_base_price").text("Price: ₱" + price);
        $("#_base_price").data("price", price);
        $("#addItemOrder").modal("show");
    });

    function updateTotalPrice() {
        const $basePrice = $("#_base_price");
        const price = parseFloat($basePrice.data("price")) || 0;
        const $quantityInput = $("#quantity");
        const quantity = parseInt($quantityInput.val()) || 0;
        const $totalPriceElement = $("#total_price");
        if (price <= 0) {
            $totalPriceElement.text("");
            return;
        }

        const totalPriceValue = price * quantity;

        const formattedPrice = totalPriceValue.toLocaleString("en-PH", {
            style: "currency",
            currency: "PHP",
            minimumFractionDigits: 2,
        });

        $totalPriceElement.text("Total Price: " + formattedPrice);
    }

    $("#quantity").on("input", updateTotalPrice);

    updateTotalPrice();

    $("#cancelAddOrder").click(function (e) {
        e.preventDefault();
        clearRestockFields();
    });

    function clearRestockFields() {
        $("#_item_id").val("");
        $("#quantity").val("");
        $("#_item_name").text("");
        $("#_base_price").text("");
        $("#total_price").text("");
        $("#_base_price").removeAttr("data-price");
    }

    $("#addOrderBtn").click(function (e) {
        e.preventDefault();
        let isValid = true;
        const $form = $("#addOrder");

        $form.find("input, number").each(function () {
            const $field = $(this);
            const value = $field.val();
            if ($field.prop("required") && (!value || !value.trim())) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        if (isValid) {
            $("#LoadingScreen").fadeIn(200);

            const _id = $("#_item_id").val();
            const quantityInput = $("#quantity");
            const newQuantity = parseInt(quantityInput.val()) || 1;

            const productName = $("#_item_name").text().trim();
            const _name = productName.replace("Product Name: ", "").trim();

            const totalPriceText = $("#total_price").text().trim();
            const newTotalPrice =
                parseFloat(totalPriceText.replace(/[^\d\.]/g, "").trim()) || 0;

            const $existingItem = $(
                `#orderList .prod-name[data-id="${_id}"]`
            ).closest(".d-flex.align-items-center.py-2.border-bottom");
            let itemExists = $existingItem.length > 0;

            const totalElement = $("#order-total-amount");
            const currentGrandTotal =
                parseFloat(totalElement.text().replace(/[^0-9.]/g, "")) || 0;
            const newGrandTotal = currentGrandTotal + newTotalPrice;
            totalElement.text("₱ " + parseFloat(newGrandTotal).toFixed(2));

            if (itemExists) {
                const qntyElement = $existingItem.find(".qnty");
                const itemTotalPriceElement = $existingItem.find(".prod-price");

                let currentQuantity = parseInt(qntyElement.text().trim()) || 0;
                let currentItemTotalTxt = itemTotalPriceElement.text().trim();
                let currentItemTotal =
                    parseFloat(currentItemTotalTxt.replace(/[^0-9.]/g, "")) ||
                    0;

                const updatedQuantity = currentQuantity + newQuantity;
                const updatedItemTotal = currentItemTotal + newTotalPrice;

                qntyElement.text(updatedQuantity);
                itemTotalPriceElement.text(
                    "₱" + parseFloat(updatedItemTotal).toFixed(2)
                );
            } else {
                const order = `
                <div class="d-flex align-items-center py-2 border-bottom">
                    <div class="flex-grow-1 me-3">
                        <h6 class="mb-0 text-primary text-muted prod-name" data-id="${_id}">${_name}</h6>
                        <small class="text-secondary prod-price">₱${parseFloat(
                            newTotalPrice || 0
                        ).toFixed(2)}</small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="width: 110px;">
                        <a href="#" class="btn btn-sm btn-danger p-1 dec-qty-btn">
                            <i class="fa-solid fa-minus"></i>
                        </a>
                        <h6 class="mb-0 mx-2 qnty">${newQuantity}</h6>
                        <a href="#" class="btn btn-sm btn-primary p-1 inc-qty-btn">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                </div>
            `;
                $("#orderList").append(order);
            }

            $("#LoadingScreen").fadeOut(200);
            clearRestockFields();
            $("#addItemOrder").modal("hide");
        }
    });

    $(document).on("click", ".dec-qty-btn", function () {
        const clickedButton = $(this);

        const orderItemRow = clickedButton.closest(
            ".d-flex.align-items-center.py-2.border-bottom"
        );

        const qntyElement = orderItemRow.find(".qnty");
        const itemTotalPriceElement = orderItemRow.find(".prod-price");
        const totalElement = $("#order-total-amount");

        let currentQuantity = parseInt(qntyElement.text().trim()) || 0;

        let itemTotalPriceTxt = itemTotalPriceElement.text().trim();
        let currentItemTotal =
            parseFloat(itemTotalPriceTxt.replace(/[^0-9.]/g, "")) || 0;

        const currentGrandTotal =
            parseFloat(totalElement.text().replace(/[^0-9.]/g, "")) || 0;

        const unitPrice =
            currentQuantity > 0 ? currentItemTotal / currentQuantity : 0;

        if (currentQuantity > 1) {
            const newGrandTotal = currentGrandTotal - unitPrice;

            const newItemTotal = currentItemTotal - unitPrice;

            qntyElement.text(currentQuantity - 1);
            itemTotalPriceElement.text(
                "₱ " + parseFloat(newItemTotal).toFixed(2)
            );

            totalElement.text("₱ " + parseFloat(newGrandTotal).toFixed(2));
        } else if (currentQuantity === 1) {
            const newGrandTotal = currentGrandTotal - currentItemTotal;

            orderItemRow.remove();

            totalElement.text("₱ " + parseFloat(newGrandTotal).toFixed(2));
        }
    });

    $(document).on("click", ".inc-qty-btn", function () {
        const clickedButton = $(this);

        const orderItemRow = clickedButton.closest(
            ".d-flex.align-items-center.py-2.border-bottom"
        );

        const qntyElement = orderItemRow.find(".qnty");
        const itemTotalPriceElement = orderItemRow.find(".prod-price");
        const totalElement = $("#order-total-amount");

        let currentQuantity = parseInt(qntyElement.text().trim()) || 0;

        let itemTotalPriceTxt = itemTotalPriceElement.text().trim();
        let currentItemTotal =
            parseFloat(itemTotalPriceTxt.replace(/[^0-9.]/g, "")) || 0;

        const currentGrandTotal =
            parseFloat(totalElement.text().replace(/[^0-9.]/g, "")) || 0;

        const unitPrice =
            currentQuantity > 0 ? currentItemTotal / currentQuantity : 0;

        if (unitPrice > 0) {
            const newGrandTotal = currentGrandTotal + unitPrice;

            const newItemTotal = currentItemTotal + unitPrice;

            qntyElement.text(currentQuantity + 1);
            itemTotalPriceElement.text(
                "₱ " + parseFloat(newItemTotal).toFixed(2)
            );

            totalElement.text("₱ " + parseFloat(newGrandTotal).toFixed(2));
        }
    });

    $(function () {
        // This handler opens the modal and triggers the data loading
        $(document).on("click", ".showTransactionsModal", function (e) {
            $("#orderTransactions").modal("show");
            loadTodayOrdersData();
        });

        // This is the main function to initialize and load the DataTable
        function loadTodayOrdersData() {
            const tableSelector = "#posOrdersTransactions";

            if ($.fn.DataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }

            $(tableSelector).DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: "/operations/pos/recent-orders",
                    type: "GET",
                    dataSrc: "data",
                    error: function (xhr, error, code) {
                        console.error(
                            "DataTables AJAX Error:",
                            xhr.responseText
                        );
                        $(tableSelector)
                            .find("tbody")
                            .html(
                                '<tr><td colspan="10" class="text-center text-danger">Failed to load orders.</td></tr>'
                            );
                    },
                },
                columns: [
                    { data: "order_id", title: "Order #" },
                    {
                        data: "items",
                        title: "Items",
                        render: function (data, type, row) {
                            if (type === "display") {
                                let itemList = data
                                    .map(
                                        (item) =>
                                            `<li>${item.quantity}x ${item.product_name}</li>`
                                    )
                                    .join("");
                                return `<ul class="list-unstyled p-0 m-0" style="font-size: 0.85rem">${itemList}</ul>`;
                            }
                            return data;
                        },
                    },
                    { data: "created_at", title: "Date" },
                    {
                        data: "total_amount",
                        title: "Amount",
                        render: $.fn.dataTable.render.number(",", ".", 2, "₱ "),
                        className: "dt-left font-weight-bold",
                    },
                    { data: "order_type", title: "Type" },
                    { data: "payment_method", title: "Payment" },
                    {
                        data: "cashier_name",
                        title: "Cashier",
                        defaultContent: "N/A",
                    },
                    {
                        data: "status",
                        title: "Status",
                        className: "font-weight-bold",
                    },
                    {
                        data: null,
                        title: "Actions",
                        orderable: false,
                        width: "8%",
                        render: function (data, type, row) {
                            let voidBtn = "";

                            if (
                                row.status ===
                                    '<span class="badge bg-warning">In Queue</span>' ||
                                row.status ===
                                    '<span class="badge bg-info">In Prep</span>'
                            ) {
                                voidBtn = ` <button class="btn btn-sm btn-danger void-order-btn" title="Void Order" data-order="${row.order_id}" data-id="${row.id}" data-status='${row.status}'><i class="fas fa-trash-alt"></i></button>`;
                            }
                            return `<div class="btn-group">${voidBtn}</div>`;
                        },
                    },
                ],
                order: [[2, "desc"]],
                language: {
                    emptyTable: "No orders placed today.",
                    zeroRecords: "No matching orders found.",
                },
                fixedColumns: true,
                scrollX: true,
            });
        }

        // This is the click handler for the void button
        $("#posOrdersTransactions").on("click", ".void-order-btn", function () {
            const orderId = $(this).data("id");
            const order_id = $(this).data("order");
            const orderStatus = $(this).data("status");
            const table = $("#posOrdersTransactions").DataTable();

            if (
                orderStatus === '<span class="badge bg-warning">In Queue</span>'
            ) {
                Swal.fire({
                    title: "Are you sure?",
                    text: `Do you want to void order #${order_id}? This action cannot be undone.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, void it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        voidOrderAjax(orderId, table);
                    }
                });
            } else if (
                orderStatus === '<span class="badge bg-info">In Prep</span>'
            ) {
                Swal.fire({
                    title: "Order in Progress!",
                    html: `Order #${order_id} is already being prepared.<br>Voiding it now will incur a charge and waste materials.<br><br><b>Do you want to proceed?</b>`,
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, void with charges",
                }).then((result) => {
                    if (result.isConfirmed) {
                        voidOrderAjax(orderId, table);
                    }
                });
            }
        });

        // This is the helper function that performs the AJAX call
        function voidOrderAjax(orderId, table) {
            $.ajax({
                url: `/operations/pos/void-order/${orderId}`,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Toast.fire("Voided!", response.message, "success");
                    table.ajax.reload();
                },
                error: function (xhr) {
                    const errorMsg = xhr.responseJSON
                        ? xhr.responseJSON.message
                        : "An error occurred.";
                    Swal.fire("Error!", errorMsg, "error");
                },
            });
        }
    });
});

$(function () {
    let orderItems = [];
    let grandTotal = 0;

    $(document).on("click", "#submit-order-btn", function (e) {
        e.preventDefault();
        orderItems = [];
        let isValid = true;

        $("#orderList")
            .find(".d-flex.align-items-center.py-2.border-bottom")
            .each(function () {
                const $itemRow = $(this);
                const productId = $itemRow.find(".prod-name").data("id");
                const productName = $itemRow.find(".prod-name").text().trim();
                const quantity =
                    parseInt($itemRow.find(".qnty").text().trim()) || 0;
                const itemPriceText = $itemRow
                    .find(".prod-price")
                    .text()
                    .trim();
                const itemTotalPrice =
                    parseFloat(itemPriceText.replace(/[^0-9.]/g, "")) || 0;
                const unitPrice = quantity > 0 ? itemTotalPrice / quantity : 0;

                if (quantity === 0 || productId === null) {
                    isValid = false;
                    return false;
                }

                orderItems.push({
                    product_id: productId,
                    name: productName,
                    quantity: quantity,
                    unit_price: parseFloat(unitPrice).toFixed(2),
                    total_price: parseFloat(itemTotalPrice).toFixed(2),
                });
            });

        grandTotal =
            parseFloat(
                $("#order-total-amount")
                    .text()
                    .replace(/[^0-9.]/g, "")
            ) || 0;

        if (isValid && orderItems.length > 0) {
            const $summaryContainer = $("#orderSummaryList");
            $summaryContainer.empty();
            orderItems.forEach((item) => {
                const itemHtml = `<div class="d-flex justify-content-between"><span>${item.quantity}x ${item.name}</span><span>₱ ${item.total_price}</span></div>`;
                $summaryContainer.append(itemHtml);
            });

            $("#modalGrandTotal").text("₱ " + grandTotal.toFixed(2));
            $("#cashReceivedInput").val("");
            $("#modalChange").text("₱ 0.00");
            $("#confirmSubmitOrder")
                .prop("disabled", true)
                .removeClass("d-none");
            $("#printReceiptBtn").addClass("d-none");

            $("#orderFinalization").modal("show");
        } else {
            Toast.fire({
                text: "The order is empty or contains invalid items. Please add products.",
                icon: "warning",
                timer: 2000,
            });
        }
    });

    $("#cashReceivedInput").on("keyup input", function () {
        const cashReceived = parseFloat($(this).val()) || 0;
        const change = cashReceived - grandTotal;

        if (cashReceived >= grandTotal) {
            $("#modalChange").text("₱ " + change.toFixed(2));
            $("#confirmSubmitOrder").prop("disabled", false);
        } else {
            $("#modalChange").text("₱ 0.00");
            $("#confirmSubmitOrder").prop("disabled", true);
        }
    });

    $("#finalizeOrderForm").on("submit", function (e) {
        e.preventDefault();
        $("#LoadingScreen").fadeIn(200);

        const orderType = $("#order_type_input").val();
        const cashReceived = parseFloat($("#cashReceivedInput").val()) || 0;
        const changeDue = cashReceived - grandTotal;

        const orderData = {
            order_items: orderItems,
            grand_total: parseFloat(grandTotal).toFixed(2),
            order_type: orderType,
            cash_received: cashReceived.toFixed(2),
            change_due: changeDue.toFixed(2),
        };

        $.ajax({
            url: "/operations/pos/submit-order",
            type: "POST",
            data: orderData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                Toast.fire({
                    text: "Order completed! ID: " + response.order_id,
                    icon: "success",
                });

                const cashierName =
                    $("#cashierNameDisplay").text().trim() || "N/A";
                generateReceipt(
                    response.order_id,
                    cashReceived,
                    changeDue,
                    cashierName
                );

                $("#confirmSubmitOrder").addClass("d-none");
                $("#printReceiptBtn").removeClass("d-none");
                $("#orderList").empty();
                $("#order-total-amount").text("₱ 0.00");
            },
            error: function (xhr) {
                alert("Error: " + xhr.responseJSON.message);
            },
            complete: function () {
                $("#LoadingScreen").fadeOut(200);
            },
        });
    });

    function generateReceipt(orderId, cash, change, cashierName) {
        let itemsHtml = "";
        orderItems.forEach((item) => {
            itemsHtml += `
                <tr>
                    <td>${item.quantity}x ${item.name}</td>
                    <td style="text-align: right;">${item.total_price}</td>
                </tr>
            `;
        });

        const receiptHtml = `
            <div style="width: 300px; font-family: monospace; font-size: 14px; color: #000; margin: 0 auto;">
                <h3 style="text-align: center;">Tinatangi Cafe</h3>
                <p style="text-align: center;">Brgy 13 Jose Abad Santos Ave, Dasmariñas, 4114 Cavite<br>Official Receipt</p>
                <hr>
                <p>
                    Order ID: ${orderId}<br>
                    Date: ${new Date().toLocaleString()}<br>
                    Cashier: ${cashierName}
                </p>
                <hr>
                <table style="width: 100%;">
                    <tbody>${itemsHtml}</tbody>
                </table>
                <hr>
                <table style="width: 100%;">
                    <tbody>
                        <tr><td>Total:</td><td style="text-align: right;">₱ ${grandTotal.toFixed(
                            2
                        )}</td></tr>
                        <tr><td>Cash:</td><td style="text-align: right;">₱ ${cash.toFixed(
                            2
                        )}</td></tr>
                        <tr><td>Change:</td><td style="text-align: right;">₱ ${change.toFixed(
                            2
                        )}</td></tr>
                    </tbody>
                </table>
                <hr>
                <p style="text-align: center;">Thank you, come again!</p>
            </div>
        `;

        $("#receipt-container").html(receiptHtml);
    }

    $(document).on("click", "#printReceiptBtn", function () {
        window.print();
    });

    $("#orderFinalization .order-type-btn").on("click", function () {
        $(this).siblings().removeClass("active");
        $(this).addClass("active");
        $("#order_type_input").val($(this).data("type"));
    });
});
