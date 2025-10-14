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
                        imageUrl = "/" + imagePath;
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
        getProducts(`/operations/pos/get-pastries-products`, "#pastriesProducts");
    }

    function getBeveragesProducts() {
        getProducts(`/operations/pos/get-beverages-products`, "#beveragesProducts");
    }

    function getMealsProducts() {
        getProducts(`/operations/pos/get-meals-products`, "#mealsProducts");
    }

    function getSnacksAndSidesProducts() {
        getProducts(`/operations/pos/get-snacks-sides-products`, "#snakcsSidesProducts");
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
    $(document).on("shown.bs.tab", "#v-pills-snacksSides-tab", function (e) {
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
                parseFloat(
                    totalPriceText.replace("Total Price: ₱", "").trim()
                ) || 0;

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

    $(document).on("click", "#submit-order-btn", function (e) {
        e.preventDefault();
        $("#LoadingScreen").fadeIn(200);

        const orderItems = [];
        let isValid = true;

        $("#orderList")
            .find(".d-flex.align-items-center.py-2.border-bottom")
            .each(function () {
                const $itemRow = $(this);

                const productId = $itemRow.find(".prod-name").data("id");
                const productName = $itemRow.find(".prod-name").text().trim();

                const quantityText = $itemRow.find(".qnty").text().trim();
                const quantity = parseInt(quantityText) || 0;

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

        const totalElement = $("#order-total-amount");
        const grandTotal =
            parseFloat(totalElement.text().replace(/[^0-9.]/g, "")) || 0;

        if (isValid && orderItems.length > 0) {
            const orderData = {
                order_items: orderItems,
                grand_total: parseFloat(grandTotal).toFixed(2),
            };

            $.ajax({
                url: "/operations/pos/submit-order",
                type: "POST",
                data: orderData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    Toast.fire({
                        text:
                            "Order completed successfully! Order ID: " +
                            response.order_id,
                        icon: "success",
                        timer: 2000,
                    });
                    $("#orderList").empty();
                    $("#order-total-amount").text("₱ 0.00");
                },
                error: function (xhr) {
                    const errorMsg = xhr.responseJSON
                        ? xhr.responseJSON.message
                        : "Failed to submit order. Please try again.";
                    alert("Error: " + errorMsg);
                },
                complete: function () {
                    $("#LoadingScreen").fadeOut(200);
                },
            });
        } else {
            $("#LoadingScreen").fadeOut(200);
            Toast.fire({
                text: "The order is empty or contains invalid items. Please add products.",
                icon: "warning",
                timer: 2000,
            });
        }
    });
});
