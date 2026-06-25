import $ from 'jquery';
import Swal from 'sweetalert2';

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

    const categoriesNavContainer = $("#v-pills-tab");
    const tabContentContainer = $("#v-pills-tabContent");
    const categoriesLoadingHtml =
        '<div class="text-center py-3 text-muted">Loading categories...</div>';
    const productsIdleHtml =
        '<div class="text-center py-5 text-muted">Select a category to view products.</div>';
    const categoriesErrorHtml =
        '<div class="text-center py-3 text-danger">Failed to load categories.</div>';

    let posCategories = [];
    const categoryProductsCache = {};
    let currentCategorySlug = null;
    let currentSortMode = "default";
    let bestSellerProductIds = new Map();

    function cacheCategoryProducts(slug, products) {
        if (!slug) {
            return;
        }
        categoryProductsCache[slug] = Array.isArray(products)
            ? products.map((product) => Object.assign({}, product))
            : [];
    }

    function getCachedCategoryProducts(slug) {
        if (!slug || !categoryProductsCache[slug]) {
            return [];
        }
        return categoryProductsCache[slug].map((product) =>
            Object.assign({}, product)
        );
    }

    function sortProductsForDisplay(products) {
        if (!Array.isArray(products)) {
            return [];
        }

        const productsCopy = products.map((product) =>
            Object.assign({}, product)
        );

        if (currentSortMode === "alphabetical") {
            return productsCopy.sort((a, b) => {
                const nameA = (a.name || "").toString().toLowerCase();
                const nameB = (b.name || "").toString().toLowerCase();
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0;
            });
        }

        if (currentSortMode === "servings") {
            return productsCopy.sort((a, b) => {
                const servingsA =
                    parseInt(a.available_servings ?? a.servings ?? 0, 10) || 0;
                const servingsB =
                    parseInt(b.available_servings ?? b.servings ?? 0, 10) || 0;
                return servingsB - servingsA;
            });
        }

        return productsCopy;
    }

    function renderProductsToContainer(products, containerSelector) {
        const $container = $(containerSelector);

        if (!Array.isArray(products) || products.length === 0) {
            const placeholderHtml = `
                <div class="col-12 text-center my-5 p-5">
                    <h3 class="text-muted">No available products in this category.</h3>
                    <p class="text-muted">Available products will appear here automatically.</p>
                </div>`;
            $container.html(placeholderHtml);
            applySearchFilter();
            return;
        }

        const sortedProducts = sortProductsForDisplay(products);
        const productsHtml = [];

        sortedProducts.forEach((element) => {
            const imagePath = element.image;
            let imageUrl;
            const productName = element.name || "";
            const productNameLower = productName.toLowerCase();

            if (imagePath && imagePath !== "N/A") {
                imageUrl = "/storage/" + imagePath;
            } else {
                imageUrl = DEFAULT_PRODUCT_IMAGE;
            }
            const rawServings =
                element.available_servings ?? element.servings ?? 0;
            const parsedServings = parseInt(rawServings, 10);
            const servings = Number.isNaN(parsedServings) ? 0 : parsedServings;
            let servingsHtml = "";

            if (servings > 0) {
                servingsHtml = `<span class="position-absolute top-0 end-0 bg-primary text-white p-1 px-2 rounded-pill" style="font-size: 0.8rem; margin: 5px;">${servings} servings</span>`;
            } else {
                servingsHtml = `
                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: rgba(220, 53, 69, 0.7); border-radius: inherit;">
                        <h5 class="text-white fw-bold">Out of Stock</h5>
                    </div>
                `;
            }

            const bestSellerRank = bestSellerProductIds.get(element.id);
            const bestSellerBadge = bestSellerRank !== undefined
                ? `<span class="position-absolute top-0 start-0 bg-warning text-dark px-2 rounded-pill" style="font-size: 0.7rem; font-weight: 600; margin: 5px; z-index: 2;">#${bestSellerRank} &#9733; Best Seller</span>`
                : "";

            productsHtml.push(`
                <div class="col" data-id="${
                    element.id
                }" data-available-servings="${servings}" data-name="${productNameLower.replace(
                /"/g,
                "&quot;"
            )}" ${servings <= 0 ? 'data-disabled="true"' : ""}>
                    <div class="card shadow h-100 product-card-fixed-size d-flex p-2 m-2 ${
                        servings <= 0 ? "border-danger" : ""
                    }">
                        ${servingsHtml}
                        ${bestSellerBadge}
                        <img src="${imageUrl}" class="card-img-top img-fluid prod-img" alt="Product Image">
                        <div class="card-body p-2 flex-grow-1">
                            <h6 class="card-title mb-1 prod-name">${productName}</h6>
                            <h6 class="text-success mb-0 prod-price">₱${parseFloat(
                                element.base_price || 0
                            ).toFixed(2)}</h6>
                        </div>
                    </div>
                </div>
            `);
        });

        $container.html(productsHtml.join(""));
        applySearchFilter();
    }

    function renderActiveCategoryProducts() {
        if (!currentCategorySlug) {
            return;
        }

        const activeCategory = posCategories.find(
            (category) => category.slug === currentCategorySlug
        );

        if (!activeCategory) {
            return;
        }

        const containerSelector = `#${activeCategory.productsContainerId}`;
        const products = getCachedCategoryProducts(activeCategory.slug);
        renderProductsToContainer(products, containerSelector);
    }

    function slugify(text) {
        if (!text) {
            return "category";
        }
        const slug = text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/[\s\W-]+/g, "-")
            .replace(/^-+|-+$/g, "");
        return slug || "category";
    }

    function renderCategoryNavigation(categories) {
        if (!Array.isArray(categories) || categories.length === 0) {
            categoriesNavContainer.html(
                '<div class="text-center py-3 text-muted">No categories available.</div>'
            );
            tabContentContainer.html(
                '<div class="text-center py-5 text-muted">No products to display.</div>'
            );
            return;
        }

        const navLinks = [];
        const tabPanes = [];

        posCategories = categories.map((category, index) => {
            const slug = category.slug || slugify(category.name || "category");
            const isAll =
                category.isAll === true ||
                slug === "all" ||
                (category.name || "").toLowerCase() === "all";
            const navId = `v-pills-${slug}-tab`;
            const paneId = `v-pills-${slug}`;
            const productsContainerId = `${slug}Products`;
            const isActive = index === 0;
            const prefetchedProducts = Array.isArray(
                category.prefetchedProducts
            )
                ? category.prefetchedProducts
                : null;

            navLinks.push(
                `<a class="nav-link ${
                    isActive ? "active" : ""
                }" id="${navId}" data-bs-toggle="pill" href="#${paneId}" role="tab" aria-controls="${paneId}" aria-selected="${isActive}" data-category-index="${index}">${
                    category.name
                }</a>`
            );

            tabPanes.push(
                `<div class="tab-pane fade ${
                    isActive ? "show active" : ""
                } py-4" id="${paneId}" role="tabpanel" aria-labelledby="${navId}">
                    <div id="${productsContainerId}" class="row row-cols-4 g-2"></div>
                </div>`
            );

            return {
                id: category.id ?? null,
                name: category.name,
                slug,
                isAll,
                navId,
                paneId,
                productsContainerId,
                prefetchedProducts,
            };
        });

        categoriesNavContainer.html(navLinks.join(""));
        tabContentContainer.html(tabPanes.join(""));
    }

    function loadProductsForCategory(category) {
        if (!category) {
            return;
        }

        currentCategorySlug = category.slug;
        const containerSelector = `#${category.productsContainerId}`;

        if (
            Array.isArray(category.prefetchedProducts) &&
            category.prefetchedProducts.length > 0
        ) {
            cacheCategoryProducts(category.slug, category.prefetchedProducts);
            category.prefetchedProducts = null;
        }

        const cachedProducts = getCachedCategoryProducts(category.slug);

        if (cachedProducts.length > 0) {
            renderProductsToContainer(cachedProducts, containerSelector);
            return;
        }

        let url = "/operations/pos/products";

        if (!category.isAll) {
            url += `?category=${encodeURIComponent(category.name)}`;
        }

        getProducts(url, containerSelector, category);
    }

    function loadPosCategories() {
        categoriesNavContainer.html(categoriesLoadingHtml);
        tabContentContainer.html(productsIdleHtml);

        $.get("/operations/pos/categories", function (response) {
            const categoryData = Array.isArray(response.data)
                ? response.data
                : [];
            const categories = [
                { id: null, name: "All", slug: "all", isAll: true },
                ...categoryData.map((item) => ({
                    id: item.id ?? null,
                    name: item.name ?? "Unnamed",
                    slug: item.slug ?? slugify(item.name ?? "category"),
                })),
            ];

            $.get("/operations/pos/monthly-best-sellers", { limit: 5 })
                .done(function (bestSellerResponse) {
                    const bestSellerProducts = Array.isArray(
                        bestSellerResponse.data
                    )
                        ? bestSellerResponse.data
                        : [];

                    bestSellerProductIds = new Map(
                        bestSellerProducts.map((p, i) => [p.id, i + 1])
                    );
                })
                .fail(function () {
                    console.warn(
                        "Warning: Unable to load monthly best sellers for POS."
                    );
                })
                .always(function () {
                    renderCategoryNavigation(categories);

                    if (posCategories.length > 0) {
                        loadProductsForCategory(posCategories[0]);
                    }
                });
        }).fail(function () {
            categoriesNavContainer.html(categoriesErrorHtml);
            tabContentContainer.html(
                '<div class="text-center py-5 text-danger">Unable to load products.</div>'
            );
        });
    }

    function applySearchFilter() {
        const query = ($("#product-search-input").val() || "")
            .toLowerCase()
            .trim();

        const activePane = $("#v-pills-tabContent .tab-pane.show.active");
        if (activePane.length === 0) {
            return;
        }

        const productContainer = activePane.find(".row.row-cols-auto.g-3");
        if (productContainer.length === 0) {
            return;
        }

        const productColumns = productContainer.children(".col[data-name]");
        const noResultsClass = "no-product-search-results";
        productContainer.find(`.${noResultsClass}`).remove();

        if (productColumns.length === 0) {
            return;
        }

        if (!query) {
            productColumns.removeClass("d-none");
            return;
        }

        let visibleCount = 0;

        productColumns.each(function () {
            const $col = $(this);
            const productName = ($col.data("name") || "").toString();

            if (productName.includes(query)) {
                $col.removeClass("d-none");
                visibleCount += 1;
            } else {
                $col.addClass("d-none");
            }
        });

        if (visibleCount === 0) {
            const safeQuery = $("<div>").text(query).html();
            const emptyState = `
                <div class="col-12 text-center my-5 p-5 text-muted ${noResultsClass}">
                    <h5>No products match "${safeQuery}".</h5>
                    <p>Try adjusting your search.</p>
                </div>`;
            productContainer.append(emptyState);
        }
    }

    function getProducts(url, id, category) {
        const loading_items = `
             <div class="col-12 text-center my-5 p-5 d-flex flex-column align-items-center">
                <div class="spinner-border" style="width: 3rem; height: 3rem"
                    role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h6 class="text-muted mt-2">Loading...</h6>
            </div>
            `;
        $(id).html(loading_items);

        $.get(url, function (response) {
            if (response.data && Array.isArray(response.data)) {
                const productsArray = response.data;
                const categorySlug =
                    category && category.slug ? category.slug : null;

                if (categorySlug) {
                    cacheCategoryProducts(categorySlug, productsArray);
                    const cached = getCachedCategoryProducts(categorySlug);
                    renderProductsToContainer(cached, id);
                } else {
                    renderProductsToContainer(productsArray, id);
                }
            } else {
                const errorHtml = `
                <div class="col-12 text-center mt-5">
                    <p class="text-danger">Error: Could not load products.</p>
                </div>`;
                $(id).html(errorHtml);
                console.error(
                    "Error: products data not found or is not an array."
                );
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load products.";
                console.error("AJAX Error:", errorMsg, xhr);
                const errorHtml = `<div class="col-12 text-center mt-5"><p class="text-danger">${errorMsg}</p></div>`;
                $(id).html(errorHtml);
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
                applySearchFilter();
            });
    }

    loadPosCategories();

    $(document).on(
        "shown.bs.tab",
        "#v-pills-tab a[data-category-index]",
        function () {
            const index = parseInt($(this).data("category-index"), 10);

            if (Number.isNaN(index) || !posCategories[index]) {
                return;
            }

            loadProductsForCategory(posCategories[index]);
        }
    );

    $(document).on("input", "#product-search-input", function () {
        applySearchFilter();
    });

    $(document).on("change", "#pos-sort-select", function () {
        const selected = $(this).val();

        if (selected === "alphabetical" || selected === "servings") {
            currentSortMode = selected;
        } else {
            currentSortMode = "default";
        }

        renderActiveCategoryProducts();
    });

    $(document).on("click", ".col", function () {
        const clickedColumn = $(this);

        if (clickedColumn.data("disabled") === true) {
            Toast.fire({
                icon: "error",
                title: "Out of Stock",
                text: "This product is currently unavailable.",
                timer: 1500,
            });
            return;
        }

        const id = clickedColumn.data("id");
        const availableServings =
            parseInt(clickedColumn.data("available-servings"), 10) || 0;
        const existingItemRow = $(
            `#orderList .prod-name[data-id="${id}"]`
        ).closest(".order-item-row");
        const existingQuantity = existingItemRow.length
            ? parseInt(existingItemRow.find(".qnty").text().trim(), 10) || 0
            : 0;

        if (availableServings > 0 && existingQuantity >= availableServings) {
            Toast.fire({
                icon: "warning",
                title: "Servings Exhausted",
                text: "No more servings available for this product.",
                timer: 1800,
            });
            return;
        }

        const productNameElement = clickedColumn.find(".prod-name");
        const productName = productNameElement.text().trim();
        const priceElement = clickedColumn.find(".prod-price");
        const priceText = priceElement.text().trim();
        const price = parseFloat(priceText.replace("₱", "").trim());

        $("#_item_id").val(id);
        $("#_item_name").text("Product Name: " + productName);
        $("#_base_price").text("Price: ₱" + price);
        $("#_base_price").data("price", price);
        const $quantityInput = $("#quantity");
        const remainingServings =
            availableServings > 0
                ? Math.max(availableServings - existingQuantity, 0)
                : 0;

        $("#addOrder")
            .data("available-servings", availableServings)
            .data("current-quantity", existingQuantity);

        if (remainingServings > 0) {
            $quantityInput.attr("max", remainingServings);
            if (
                !$quantityInput.val() ||
                parseInt($quantityInput.val(), 10) > remainingServings
            ) {
                $quantityInput.val(1);
            }
        } else {
            $quantityInput.removeAttr("max");
            $quantityInput.val(1);
        }

        updateTotalPrice();
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
        $("#quantity").removeAttr("max");
        $("#addOrder")
            .removeData("available-servings")
            .removeData("current-quantity");
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
            const basePrice = parseFloat($("#_base_price").data("price")) || 0;
            const quantityInput = $("#quantity");
            const newQuantity = parseInt(quantityInput.val()) || 1;
            const modalAvailable =
                parseInt($("#addOrder").data("available-servings"), 10) || 0;
            const modalExisting =
                parseInt($("#addOrder").data("current-quantity"), 10) || 0;
            const requestedTotal = modalExisting + newQuantity;

            if (modalAvailable > 0 && requestedTotal > modalAvailable) {
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    icon: "error",
                    title: "Not enough servings",
                    text: "Quantity exceeds available servings.",
                });
                return;
            }

            const productName = $("#_item_name").text().trim();
            const _name = productName.replace("Product Name: ", "").trim();

            const totalPriceText = $("#total_price").text().trim();
            const newTotalPrice =
                parseFloat(totalPriceText.replace(/[^0-9.]/g, "").trim()) || 0;

            const unitPrice =
                newQuantity > 0 ? newTotalPrice / newQuantity : basePrice;

            const $existingItem = $(
                `#orderList .prod-name[data-id="${_id}"]`
            ).closest(".order-item-row");
            let itemExists = $existingItem.length > 0;

            const totalElement = $("#order-total-amount");
            const currentGrandTotal =
                parseFloat(totalElement.text().replace(/[^0-9.]/g, "")) || 0;
            const newGrandTotal = currentGrandTotal + newTotalPrice;
            totalElement.text("₱ " + parseFloat(newGrandTotal).toFixed(2));

            if (itemExists) {
                const qntyElement = $existingItem.find(".qnty");
                const itemTotalPriceElement = $existingItem.find(".prod-price");
                const unitPriceElement = $existingItem.find(".unit-price");

                let currentQuantity = parseInt(qntyElement.text().trim()) || 0;
                let currentItemTotalTxt = itemTotalPriceElement.text().trim();
                let currentItemTotal =
                    parseFloat(currentItemTotalTxt.replace(/[^0-9.]/g, "")) ||
                    0;

                const updatedQuantity = currentQuantity + newQuantity;
                if (modalAvailable > 0 && updatedQuantity > modalAvailable) {
                    $("#LoadingScreen").fadeOut(200);
                    Toast.fire({
                        icon: "error",
                        title: "Not enough servings",
                        text: "Quantity exceeds available servings.",
                    });
                    return;
                }
                const updatedItemTotal = currentItemTotal + newTotalPrice;

                qntyElement.text(updatedQuantity);
                itemTotalPriceElement.text(
                    "₱" + parseFloat(updatedItemTotal).toFixed(2)
                );
                unitPriceElement.text("₱" + parseFloat(unitPrice).toFixed(2));

                $existingItem
                    .addClass("order-item-row")
                    .attr("data-available-servings", modalAvailable)
                    .data("available-servings", modalAvailable);
            } else {
                $("#orderList").find("#order-placeholder").remove();
                const order = `
                <div class="d-flex align-items-center py-2 border-bottom order-item-row" data-product-id="${_id}" data-available-servings="${modalAvailable}">
                    <div class="flex-grow-1 me-3">
                        <h6 class="mb-0 text-primary text-muted prod-name" data-id="${_id}">${_name}</h6>
                        <small class="text-muted d-block unit-price">₱${basePrice.toFixed(
                            2
                        )} each</small>
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

        const orderItemRow = clickedButton.closest(".order-item-row");

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

        const orderItemRow = clickedButton.closest(".order-item-row");

        const qntyElement = orderItemRow.find(".qnty");
        const itemTotalPriceElement = orderItemRow.find(".prod-price");
        const totalElement = $("#order-total-amount");

        let currentQuantity = parseInt(qntyElement.text().trim()) || 0;
        const availableServings =
            parseInt(orderItemRow.data("available-servings"), 10) || 0;

        if (availableServings > 0 && currentQuantity >= availableServings) {
            Toast.fire({
                icon: "warning",
                title: "Limit reached",
                text: "No more servings available for this product.",
                timer: 1800,
            });
            return;
        }

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
        $(document).on("click", ".showTransactionsModal", function (e) {
            $("#orderTransactions").modal("show");
            loadTodayOrdersData();
        });

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
    $(function () {
        const RECEIPT_META = window.POS_RECEIPT_META || {
            companyName: "Tinatangi Cafe",
            branchName: "Tinatangi Cafe - Dasmariñas",
            address: "Brgy 13 Jose Abad Santos Ave, Dasmariñas, Cavite 4114",
            vatTin: "000-000-000-000",
            accrNo: "000-00000000-00000",
            permitNo: "0000-0000-000-00000",
            serialNo: "000 0 000 000000",
            contactNumber: "(+63) 915 796 8729",
            hotline: "(+63) 960 216 4109",
            email: "tinatangicafe@gmail.com",
            website: "www.tinatangi.site",
            vatRate: 0.12,
        };

        let orderItems = [];
        let grandTotal = 0;

        /* ─── Discount state ─── */
        let activeDiscounts = [];
        let appliedDiscount = null;
        let discountAmount  = 0;

        /* ─── Government discount state ─── */
        let govDiscountTypes   = [];
        let appliedGovDiscount = null;
        let govDiscountAmount  = 0;

        function fetchActiveDiscounts() {
            $.get('/operations/pos/active-discounts', function (res) {
                activeDiscounts = res.data || [];
            });
        }
        fetchActiveDiscounts();

        function fetchGovernmentDiscountTypes() {
            $.get('/operations/pos/government-discount-types', function (res) {
                govDiscountTypes = res.data || [];
                renderGovDiscountButtons();
            });
        }
        fetchGovernmentDiscountTypes();

        function calcDiscountAmount(discount) {
            var productIds = (discount.product_ids || []).map(Number);
            var isAll = discount.applicable_to !== 'specific';
            var qualifying = 0;
            orderItems.forEach(function (item) {
                if (isAll || productIds.indexOf(Number(item.product_id)) !== -1) {
                    qualifying += parseFloat(item.total_price);
                }
            });
            if (discount.discount_type === 'percentage') {
                return Math.round(qualifying * (parseFloat(discount.discount_value) / 100) * 100) / 100;
            } else {
                return Math.min(parseFloat(discount.discount_value), qualifying);
            }
        }

        function applyDiscount(discount) {
            clearGovDiscount(); // mutual exclusivity with gov discount
            appliedDiscount = discount;
            discountAmount  = calcDiscountAmount(discount);
            var netTotal    = Math.max(0, grandTotal - discountAmount);

            $('#discount-label').text(discount.title);
            $('#discount-amount-display').text('-₱' + discountAmount.toFixed(2));
            $('#discount-panel').removeClass('d-none');
            $('#discount-chooser').addClass('d-none');
            $('#applied_discount_id').val(discount.id);
            $('#applied_discount_amount').val(discountAmount.toFixed(2));

            $('#modalGrandTotal').text('₱ ' + netTotal.toFixed(2));
            var cash = parseFloat($('#cashReceivedInput').val()) || 0;
            if (cash >= netTotal) {
                $('#modalChange').text('₱ ' + (cash - netTotal).toFixed(2));
                $('#confirmSubmitOrder').prop('disabled', false);
            } else {
                $('#modalChange').text('₱ 0.00');
                $('#confirmSubmitOrder').prop('disabled', true);
            }
        }

        function clearDiscount() {
            appliedDiscount = null;
            discountAmount  = 0;
            $('#discount-panel').addClass('d-none');
            $('#discount-chooser').addClass('d-none');
            $('#applied_discount_id').val('');
            $('#applied_discount_amount').val('0');
            var netTotal = Math.max(0, grandTotal - govDiscountAmount);
            $('#modalGrandTotal').text('₱ ' + netTotal.toFixed(2));
            var cash = parseFloat($('#cashReceivedInput').val()) || 0;
            if (cash >= netTotal) {
                $('#modalChange').text('₱ ' + (cash - netTotal).toFixed(2));
                $('#confirmSubmitOrder').prop('disabled', false);
            } else {
                $('#modalChange').text('₱ 0.00');
                $('#confirmSubmitOrder').prop('disabled', true);
            }
            renderGovDiscountButtons();
        }

        /* ─── Government discount functions ─── */
        function renderGovDiscountButtons() {
            var $buttons = $('#gov-discount-buttons');
            $buttons.find('.gov-disc-btn').remove();
            govDiscountTypes.forEach(function (t) {
                $buttons.append(
                    `<button type="button" class="btn btn-sm btn-outline-success gov-disc-btn"
                             data-id="${t.id}" data-name="${t.name}" data-pct="${t.percentage}"
                             style="font-size:.75rem;padding:2px 8px">
                        ${t.name} (${t.percentage}%)
                    </button>`
                );
            });
            if (govDiscountTypes.length > 0) {
                $('#gov-discount-section').removeClass('d-none');
                $('#gov-discount-buttons').removeClass('d-none');
            }
        }

        function applyGovDiscount(type) {
            clearDiscount(); // mutual exclusivity: remove announcement discount
            appliedGovDiscount = type;
            govDiscountAmount  = Math.round(grandTotal * (type.percentage / 100) * 100) / 100;
            var netTotal = Math.max(0, grandTotal - govDiscountAmount);

            $('#gov-discount-label').text(type.name + ' Discount');
            $('#gov-discount-sublabel').text(type.percentage + '% off total');
            $('#gov-discount-amount-display').text('-₱' + govDiscountAmount.toFixed(2));
            $('#gov-discount-panel').removeClass('d-none');
            $('#gov-discount-buttons').addClass('d-none');
            $('#applied_gov_discount_type_id').val(type.id);
            $('#applied_gov_discount_amount').val(govDiscountAmount.toFixed(2));

            $('#modalGrandTotal').text('₱ ' + netTotal.toFixed(2));
            var cash = parseFloat($('#cashReceivedInput').val()) || 0;
            if (cash >= netTotal) {
                $('#modalChange').text('₱ ' + (cash - netTotal).toFixed(2));
                $('#confirmSubmitOrder').prop('disabled', false);
            } else {
                $('#modalChange').text('₱ 0.00');
                $('#confirmSubmitOrder').prop('disabled', true);
            }
        }

        function clearGovDiscount() {
            appliedGovDiscount = null;
            govDiscountAmount  = 0;
            $('#gov-discount-panel').addClass('d-none');
            $('#applied_gov_discount_type_id').val('');
            $('#applied_gov_discount_amount').val('0');
            if (govDiscountTypes.length > 0) {
                $('#gov-discount-buttons').removeClass('d-none');
            }
            var netTotal = Math.max(0, grandTotal - discountAmount);
            $('#modalGrandTotal').text('₱ ' + netTotal.toFixed(2));
            var cash = parseFloat($('#cashReceivedInput').val()) || 0;
            if (cash >= netTotal) {
                $('#modalChange').text('₱ ' + (cash - netTotal).toFixed(2));
                $('#confirmSubmitOrder').prop('disabled', false);
            } else {
                $('#modalChange').text('₱ 0.00');
                $('#confirmSubmitOrder').prop('disabled', true);
            }
        }

        $(document).on('click', '.gov-disc-btn', function () {
            var id = parseInt($(this).data('id'));
            var type = govDiscountTypes.find(function (t) { return t.id === id; });
            if (type) applyGovDiscount(type);
        });

        $(document).on('click', '#btn-remove-gov-discount', function () {
            clearGovDiscount();
        });

        function evaluateDiscounts() {
            if (!orderItems.length) { clearDiscount(); return; }

            var eligible = activeDiscounts.filter(function (d) {
                if (d.applicable_to === 'specific') {
                    var pids = (d.product_ids || []).map(Number);
                    var hasMatch = orderItems.some(function (item) {
                        return pids.indexOf(Number(item.product_id)) !== -1;
                    });
                    if (!hasMatch) return false;
                }
                if (d.min_spend && grandTotal < parseFloat(d.min_spend)) return false;
                return true;
            });

            if (eligible.length === 1) {
                applyDiscount(eligible[0]);
            } else if (eligible.length > 1) {
                clearDiscount();
                var html = '';
                eligible.forEach(function (d) {
                    var amt = calcDiscountAmount(d);
                    html += `<button type="button" class="btn btn-sm btn-outline-warning text-start discount-option-btn" data-idx="${activeDiscounts.indexOf(d)}">
                                <strong>${d.title}</strong>
                                <span class="float-end text-success">-₱${amt.toFixed(2)}</span>
                             </button>`;
                });
                $('#discount-options-list').html(html);
                $('#discount-chooser').removeClass('d-none');
            } else {
                clearDiscount();
            }
        }

        $(document).on('click', '.discount-option-btn', function () {
            var idx = parseInt($(this).data('idx'));
            if (activeDiscounts[idx]) {
                applyDiscount(activeDiscounts[idx]);
            }
        });

        $(document).on('click', '#btn-remove-discount', function () {
            clearDiscount();
        });

        $(document).on("click", "#submit-order-btn", function (e) {
            e.preventDefault();
            orderItems = [];
            let isValid = true;

            $("#orderList")
                .find(".order-item-row")
                .each(function () {
                    const $itemRow = $(this);
                    const productId = $itemRow.find(".prod-name").data("id");
                    const productName = $itemRow
                        .find(".prod-name")
                        .text()
                        .trim();
                    const quantity =
                        parseInt($itemRow.find(".qnty").text().trim()) || 0;
                    const itemPriceText = $itemRow
                        .find(".prod-price")
                        .text()
                        .trim();
                    const itemTotalPrice =
                        parseFloat(itemPriceText.replace(/[^0-9.]/g, "")) || 0;
                    const unitPrice =
                        quantity > 0 ? itemTotalPrice / quantity : 0;

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
                clearDiscount();
                clearGovDiscount();
                evaluateDiscounts();
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
            const netTotal     = Math.max(0, grandTotal - discountAmount - govDiscountAmount);
            const change       = cashReceived - netTotal;

            if (cashReceived >= netTotal) {
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

            const orderType   = $("#order_type_input").val();
            const cashReceived = parseFloat($("#cashReceivedInput").val()) || 0;
            const netTotal    = Math.max(0, grandTotal - discountAmount - govDiscountAmount);
            const changeDue   = cashReceived - netTotal;

            const orderData = {
                order_items:          orderItems,
                grand_total:          parseFloat(grandTotal).toFixed(2),
                order_type:           orderType,
                cash_received:        cashReceived.toFixed(2),
                change_due:           Math.max(0, changeDue).toFixed(2),
                discount_id:          parseInt($('#applied_discount_id').val()) || null,
                discount_amount:      parseFloat($('#applied_discount_amount').val()) || 0,
                gov_discount_type_id: parseInt($('#applied_gov_discount_type_id').val()) || null,
                gov_discount_amount:  parseFloat($('#applied_gov_discount_amount').val()) || 0,
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
                        text: "Order completed! ID: " + response.order_id,
                        icon: "success",
                    });
                    renderActiveCategoryProducts();
                    loadPosCategories();
                    fetchActiveDiscounts();
                    clearGovDiscount();
                    const cashierName =
                        $("#cashierNameDisplay").text().trim() || "N/A";
                    generateReceipt(
                        response.order_id,
                        cashReceived,
                        changeDue,
                        cashierName
                    );
                    $("#closeBtn").removeClass("d-none");
                    $("#printReceiptBtn").removeClass("d-none");
                    $("#cancelBtn").addClass("d-none");
                    $("#confirmSubmitOrder").addClass("d-none");
                    $("#orderList").empty();
                    $("#order-total-amount").text("₱ 0.00");

                    const activeTab = $(".nav-pills .nav-link.active");
                    if (activeTab.length > 0) {
                        activeTab.trigger("shown.bs.tab");
                    } else if (posCategories.length > 0) {
                        loadProductsForCategory(posCategories[0]);
                    }
                },
                error: function (xhr) {
                    Toast.fire("Error: " + xhr.responseJSON.message);
                },
                complete: function () {
                    $("#LoadingScreen").fadeOut(200);
                },
            });
        });

        function formatCurrency(value) {
            return (
                "₱ " +
                Number(value || 0)
                    .toFixed(2)
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            );
        }

        function formatDateTime(date) {
            return date.toLocaleDateString("en-PH", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            });
        }

        function formatTime(date) {
            return date.toLocaleTimeString("en-PH", {
                hour: "2-digit",
                minute: "2-digit",
            });
        }

        function generateReceipt(orderId, cash, change, cashierName) {
            const now = new Date();
            const guestCount = Math.max(orderItems.length, 1);
            const totalDiscountAmt = discountAmount + govDiscountAmount;
            const netAmount = Math.max(0, grandTotal - totalDiscountAmt);
            const vatBase = RECEIPT_META.vatRate
                ? grandTotal / (1 + RECEIPT_META.vatRate)
                : grandTotal;
            const vatAmount = RECEIPT_META.vatRate ? grandTotal - vatBase : 0;

            var discountLabel = '';
            if (discountAmount > 0 && appliedDiscount) {
                discountLabel = 'Promo: ' + appliedDiscount.title;
            } else if (govDiscountAmount > 0 && appliedGovDiscount) {
                discountLabel = appliedGovDiscount.name + ' (' + appliedGovDiscount.percentage + '%)';
            }
            const discountRowHtml = totalDiscountAmt > 0
                ? `<tr>
                        <td>${discountLabel}</td>
                        <td class="text-end" style="color:#2a7a2a">-${formatCurrency(totalDiscountAmt)}</td>
                   </tr>`
                : '';

            const itemsHtml = orderItems
                .map(
                    (item) => `
                    <tr>
                        <td>${item.quantity} ${item.name}</td>
                        <td class="text-end">${formatCurrency(
                            parseFloat(item.total_price)
                        )}</td>
                    </tr>
                `
                )
                .join("");

            const receiptHtml = `
            <style>
                #receipt-container .pos-receipt {
                    width: 320px;
                    margin: 0 auto;
                    font-family: "Courier New", Courier, monospace;
                    font-size: 13px;
                    color: #000;
                    padding: 12px 10px 24px;
                }
                #receipt-container .pos-receipt h2,
                #receipt-container .pos-receipt h3,
                #receipt-container .pos-receipt p {
                    margin: 0;
                    text-align: center;
                }
                #receipt-container .pos-receipt hr {
                    border: none;
                    border-top: 1px solid #000;
                    margin: 8px 0;
                }
                #receipt-container .pos-receipt table {
                    width: 100%;
                    border-collapse: collapse;
                }
                #receipt-container .pos-receipt td {
                    padding: 2px 0;
                }
                #receipt-container .text-end {
                    text-align: right;
                }
                #receipt-container .meta-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    row-gap: 2px;
                    column-gap: 8px;
                }
                #receipt-container .meta-grid span {
                    display: block;
                    text-align: left;
                }
                #receipt-container .section-heading {
                    text-transform: uppercase;
                    font-weight: bold;
                    text-align: center;
                    margin: 6px 0 4px;
                }
            </style>
            <div class="pos-receipt">
                <h3>${RECEIPT_META.companyName}</h3>
                <p>${RECEIPT_META.branchName}</p>
                <p>${RECEIPT_META.address}</p>
                <p>VAT Reg TIN: ${RECEIPT_META.vatTin}</p>
                <p>ACCR. NO.: ${RECEIPT_META.accrNo}</p>
                <div class="meta-grid" style="margin-top:6px;">
                    <span>Serial #: ${RECEIPT_META.serialNo}</span>
                    <span>Permit #: ${RECEIPT_META.permitNo}</span>
                    <span>Date: ${formatDateTime(now)}</span>
                    <span>Time: ${formatTime(now)}</span>
                </div>
                <hr>
                <div style="text-align:left;">
                    <p>Cashier: ${cashierName}</p>
                    <p>Check #: ${orderId}</p>
                    <p>Guests: ${guestCount}</p>
                    <p>Official Receipt #: ${orderId}</p>
                </div>
                <hr>
                <div style="text-align:left;">
                    <p class="section-heading">Customer Information</p>
                    <p>Walk-in Customer</p>
                    <p>${RECEIPT_META.branchName}</p>
                </div>
                <hr>
                <p class="section-heading">Items on Ticket: ${
                    orderItems.length
                }</p>
                <table>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                </table>
                <hr>
                <table>
                    <tbody>
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">${formatCurrency(vatBase)}</td>
                        </tr>
                        <tr>
                            <td>VAT ${
                                RECEIPT_META.vatRate
                                    ? `(${(RECEIPT_META.vatRate * 100).toFixed(0)}%)`
                                    : ""
                            }</td>
                            <td class="text-end">${formatCurrency(vatAmount)}</td>
                        </tr>
                        ${discountRowHtml}
                        <tr>
                            <td><strong>Amount Due</strong></td>
                            <td class="text-end"><strong>${formatCurrency(netAmount)}</strong></td>
                        </tr>
                        <tr>
                            <td>Cash</td>
                            <td class="text-end">${formatCurrency(cash)}</td>
                        </tr>
                        <tr>
                            <td>Change</td>
                            <td class="text-end">${formatCurrency(change)}</td>
                        </tr>
                    </tbody>
                </table>
                <hr>
                <p>Prices inclusive of 10% service charge</p>
                <p>12% VAT Included</p>
                <p>Thank you and please come again.</p>
                <p>For feedback call ${RECEIPT_META.hotline}</p>
                <p>Email: ${RECEIPT_META.email}</p>
                <p>Visit us at ${RECEIPT_META.website}</p>
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
});
