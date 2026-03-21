import $ from 'jquery';
import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    const recentItemsTable = $("#recentItems").DataTable({
        autoWidth: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/inventory/recent-items",
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
                width: "40px",
                orderable: false,
            },
            { data: "sku", className: "dt-left" },
            { data: "item_name", className: "dt-left" },
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
                data: "expiration_date",
                className: "dt-left",
                render: function (data, type, row) {
                    if (!data) return '<span class="text-muted">—</span>';
                    if (type === "sort" || type === "type") return data;
                    const today = new Date(); today.setHours(0, 0, 0, 0);
                    const expDate = new Date(data);
                    const diffDays = Math.ceil((expDate - today) / 86400000);
                    if (diffDays < 0)
                        return `<span class="badge bg-danger">Expired</span>`;
                    if (diffDays <= 30)
                        return `<span class="badge bg-warning text-dark">${data}</span>`;
                    return `<span class="text-success">${data}</span>`;
                },
            },
            {
                data: "status",
                className: "text-center",
                width: "130px",
            },
            {
                data: "received_at",
                className: "dt-left",
                render: function (data, type, row) {
                    if (type === "sort" || type === "type") return row.received_at_raw || "";
                    if (!data) return '<span class="text-muted">—</span>';
                    return `<span class="text-muted small">${data}</span>`;
                },
            },
        ],
        order: [[8, "desc"]],
    });

    const DEFAULT_PRODUCT_IMAGE = "/logo.png";
    const numberFormatter = new Intl.NumberFormat("en-US");
    const currencyFormatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
    const hasBestSellerSection = $("#inventory-best-seller-summary").length > 0;
    const bestSellerState = {
        weekly: null,
        monthly: null,
    };
    let activeOverlayMode = null;

    const $bestSellerRange = $("#inventory-best-seller-range");
    const $bestSellerOverlay = $("#inventory-best-seller-overlay");
    const $bestSellerOverlayGrid = $("#inventory-best-seller-overlay-grid");
    const $bestSellerOverlayTitle = $("#inventory-best-seller-overlay-title");
    const $bestSellerOverlaySubtitle = $(
        "#inventory-best-seller-overlay-subtitle"
    );

    const summaryElements = {
        weekly: {
            title: $("#inventory-weekly-title"),
            category: $("#inventory-weekly-category"),
            units: $("#inventory-weekly-units"),
            revenue: $("#inventory-weekly-revenue"),
            image: $("#inventory-weekly-image"),
            rank: $("#inventory-weekly-rank"),
            card: $('.best-seller-summary-card[data-mode="weekly"]'),
        },
        monthly: {
            title: $("#inventory-monthly-title"),
            category: $("#inventory-monthly-category"),
            units: $("#inventory-monthly-units"),
            revenue: $("#inventory-monthly-revenue"),
            image: $("#inventory-monthly-image"),
            rank: $("#inventory-monthly-rank"),
            card: $('.best-seller-summary-card[data-mode="monthly"]'),
        },
    };

    function formatNumber(value) {
        if (value === null || value === undefined || value === "") {
            return "0";
        }
        const numeric = Number(value);
        if (Number.isNaN(numeric)) {
            return "0";
        }
        return numberFormatter.format(numeric);
    }

    function formatCurrencyValue(value) {
        if (value === null || value === undefined || value === "") {
            return currencyFormatter.format(0);
        }
        const numeric = Number(value);
        if (Number.isNaN(numeric)) {
            return currencyFormatter.format(0);
        }
        return currencyFormatter.format(numeric);
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return "";
        }
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function resolveProductImage(imagePath) {
        if (!imagePath || imagePath === "N/A") {
            return DEFAULT_PRODUCT_IMAGE;
        }

        if (/^https?:\/\//i.test(imagePath)) {
            return imagePath;
        }

        const sanitized = String(imagePath).replace(/^\/+/, "");

        if (sanitized.startsWith("storage/")) {
            return `/${sanitized}`;
        }

        if (sanitized.startsWith("app/public/")) {
            return `/storage/${sanitized.replace(/^app\/public\//, "")}`;
        }

        if (sanitized.startsWith("public/")) {
            return `/storage/${sanitized.replace(/^public\//, "")}`;
        }

        return `/storage/app/public/${sanitized}`;
    }

    function flattenPeriodItems(periodData) {
        if (!periodData || !Array.isArray(periodData.categories)) {
            return [];
        }

        return periodData.categories.flatMap((category) => {
            const items = Array.isArray(category.items) ? category.items : [];
            return items
                .map((item) => ({
                    ...item,
                    category_id: category.category_id,
                    category_name: category.category_name,
                }))
                .filter((item) => Number(item.total_units) > 0);
        });
    }

    function limitToSingleBestPerCategory(periodData) {
        if (!periodData || !Array.isArray(periodData.categories)) {
            return periodData;
        }

        const updatedCategories = periodData.categories
            .map((category) => {
                const items = Array.isArray(category.items)
                    ? category.items
                    : [];
                if (!items.length) {
                    return null;
                }

                const sorted = [...items].sort(
                    (a, b) =>
                        Number(b.total_units || 0) - Number(a.total_units || 0)
                );
                const best = sorted[0];

                if (!best || Number(best.total_units) <= 0) {
                    return null;
                }

                return {
                    ...category,
                    items: [best],
                };
            })
            .filter(Boolean);

        return {
            ...periodData,
            categories: updatedCategories,
        };
    }

    function assignGlobalRanks(periodData) {
        if (!periodData || !Array.isArray(periodData.categories)) {
            return periodData;
        }

        const limited = limitToSingleBestPerCategory(periodData);
        const items = flattenPeriodItems(limited);
        const sorted = [...items].sort(
            (a, b) => Number(b.total_units || 0) - Number(a.total_units || 0)
        );

        const rankMap = new Map();
        sorted.forEach((item, index) => {
            const key = `${item.category_id}-${item.product_id}`;
            rankMap.set(key, index + 1);
        });

        const rankedCategories = (limited.categories || []).map((category) => {
            const rankedItems = (category.items || []).map((item) => {
                const key = `${category.category_id}-${item.product_id}`;
                const globalRank = rankMap.get(key) ?? null;
                return {
                    ...item,
                    rank: globalRank,
                    global_rank: globalRank,
                };
            });

            return {
                ...category,
                items: rankedItems,
            };
        });

        return {
            ...limited,
            categories: rankedCategories,
        };
    }

    function getHighlightItem(periodData) {
        const items = flattenPeriodItems(periodData);
        if (!items.length) {
            return null;
        }

        return [...items].sort(
            (a, b) => Number(b.total_units) - Number(a.total_units)
        )[0];
    }

    function applySummaryState(mode, state) {
        const elements = summaryElements[mode];
        if (!hasBestSellerSection || !elements) {
            return;
        }

        elements.title.text(state.title ?? "-");
        elements.category.text(state.category ?? "-");
        elements.units.text(state.units ?? "0");
        elements.revenue.text(state.revenue ?? currencyFormatter.format(0));
        elements.image.attr("src", state.image || DEFAULT_PRODUCT_IMAGE);
        if (elements.rank && elements.rank.length) {
            elements.rank.text(state.rank ?? "-");
        }

        const interactive = Boolean(state.interactive);
        elements.card.css("pointer-events", interactive ? "auto" : "none");
        elements.card.css("cursor", interactive ? "pointer" : "not-allowed");
        elements.card.toggleClass("opacity-50", !interactive);
        elements.card.attr("aria-disabled", interactive ? "false" : "true");
        elements.card.attr("data-has-data", interactive ? "true" : "false");
    }

    function setSummaryCardLoading(mode) {
        applySummaryState(mode, {
            title: "Loading...",
            category: "Please wait",
            units: "—",
            revenue: "—",
            image: DEFAULT_PRODUCT_IMAGE,
            rank: null,
            interactive: false,
        });
    }

    function setSummaryCardError(mode) {
        applySummaryState(mode, {
            title: "Failed to load",
            category: "We could not retrieve this data.",
            units: "—",
            revenue: "—",
            image: DEFAULT_PRODUCT_IMAGE,
            rank: null,
            interactive: false,
        });
    }

    function setSummaryCardEmpty(mode) {
        applySummaryState(mode, {
            title: "No sales yet",
            category: "No best sellers recorded",
            units: "0",
            revenue: currencyFormatter.format(0),
            image: DEFAULT_PRODUCT_IMAGE,
            rank: null,
            interactive: false,
        });
    }

    function updateSummaryRangeLabel() {
        if (!hasBestSellerSection || !$bestSellerRange.length) {
            return;
        }
        const weeklyLabel = bestSellerState.weekly?.label
            ? `Weekly: ${bestSellerState.weekly.label}`
            : "Weekly: No data yet";
        const monthlyLabel = bestSellerState.monthly?.label
            ? `Monthly: ${bestSellerState.monthly.label}`
            : "Monthly: No data yet";

        $bestSellerRange.text(`${weeklyLabel} • ${monthlyLabel}`);
    }

    function updateSummaryCard(mode) {
        const periodData = bestSellerState[mode];
        if (!periodData) {
            setSummaryCardError(mode);
            return;
        }

        const highlight = getHighlightItem(periodData);
        if (!highlight) {
            setSummaryCardEmpty(mode);
            return;
        }

        applySummaryState(mode, {
            title: highlight.product_name || "Unnamed Product",
            category: highlight.category_name || "Uncategorized",
            units: formatNumber(highlight.total_units),
            revenue: formatCurrencyValue(highlight.total_revenue),
            image: resolveProductImage(highlight.image),
            rank: highlight.global_rank || highlight.rank || 1,
            interactive: true,
        });
    }

    function renderOverlay(mode) {
        if (!hasBestSellerSection || !$bestSellerOverlay.length) {
            return;
        }

        const periodData = bestSellerState[mode];
        const overlayTitle =
            mode === "weekly" ? "Weekly Best Sellers" : "Monthly Best Sellers";
        const periodLabel = periodData?.label || "Current period";

        $bestSellerOverlayTitle.text(overlayTitle);
        $bestSellerOverlaySubtitle.text(
            `Category leaders ranked by units sold for ${periodLabel}.`
        );

        const items = flattenPeriodItems(periodData);
        if (!items.length) {
            $bestSellerOverlayGrid.html(`
                <div class="col-12">
                    <div class="text-center text-muted py-5">No sales data is available for this period yet.</div>
                </div>
            `);
            return;
        }

        const sortedItems = [...items].sort((a, b) => {
            const rankComparison =
                Number(a.global_rank || a.rank || 0) -
                Number(b.global_rank || b.rank || 0);
            if (rankComparison !== 0) {
                return rankComparison;
            }
            return Number(b.total_units || 0) - Number(a.total_units || 0);
        });

        const cardsHtml = sortedItems
            .map((item) => {
                const imageUrl = resolveProductImage(item.image);
                const totalUnits = formatNumber(item.total_units);
                const totalRevenue = formatCurrencyValue(item.total_revenue);
                const averagePrice = formatCurrencyValue(
                    item.average_unit_price
                );
                return `
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="position-relative me-3" style="width:64px; height:64px;">
                                        <img src="${imageUrl}" alt="${escapeHtml(
                    item.product_name || "Best seller"
                )}" class="rounded-circle w-100 h-100 object-fit-cover border border-2 border-light">
                                        <span class="badge bg-success position-absolute top-0 start-0 translate-middle rounded-pill px-2 py-1">#${escapeHtml(
                                            item.global_rank || item.rank || "-"
                                        )}</span>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">${escapeHtml(
                                            item.product_name ||
                                                "Unnamed Product"
                                        )}</h5>
                                        <small class="text-muted d-block">${escapeHtml(
                                            item.category_name ||
                                                "Uncategorized"
                                        )}</small>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                        <span>Units Sold</span>
                                        <span class="fw-semibold text-dark">${totalUnits}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-1">
                                        <span>Total Revenue</span>
                                        <span class="fw-semibold text-dark">${totalRevenue}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <span>Avg Unit Price</span>
                                        <span class="fw-semibold text-dark">${averagePrice}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            })
            .join("");

        $bestSellerOverlayGrid.html(cardsHtml);
    }

    function openBestSellerOverlay(mode) {
        if (!hasBestSellerSection || !$bestSellerOverlay.length) {
            return;
        }

        const periodData = bestSellerState[mode];
        if (!periodData) {
            return;
        }

        renderOverlay(mode);
        activeOverlayMode = mode;
        $bestSellerOverlay.addClass("active").attr("aria-hidden", "false");
        $("body").addClass("best-seller-overlay-open");
        $bestSellerOverlay.trigger("focus");
    }

    function closeBestSellerOverlay() {
        if (!hasBestSellerSection || !$bestSellerOverlay.length) {
            return;
        }

        activeOverlayMode = null;
        $bestSellerOverlay.removeClass("active").attr("aria-hidden", "true");
        $("body").removeClass("best-seller-overlay-open");
    }

    function loadBestSellers() {
        if (!hasBestSellerSection) {
            return;
        }

        setSummaryCardLoading("weekly");
        setSummaryCardLoading("monthly");
        if ($bestSellerRange.length) {
            $bestSellerRange.text("Loading best seller highlights...");
        }

        $.ajax({
            url: "/inventory/best-sellers",
            type: "GET",
            data: {
                limit: 1,
            },
            dataType: "json",
            success: function (response) {
                bestSellerState.weekly = assignGlobalRanks(
                    response.weekly || null
                );
                bestSellerState.monthly = assignGlobalRanks(
                    response.monthly || null
                );

                updateSummaryCard("weekly");
                updateSummaryCard("monthly");
                updateSummaryRangeLabel();

                if (activeOverlayMode) {
                    renderOverlay(activeOverlayMode);
                }
            },
            error: function () {
                bestSellerState.weekly = null;
                bestSellerState.monthly = null;
                setSummaryCardError("weekly");
                setSummaryCardError("monthly");
                if ($bestSellerRange.length) {
                    $bestSellerRange.text(
                        "Unable to load best seller data right now."
                    );
                }
                if ($bestSellerOverlayGrid.length) {
                    $bestSellerOverlayGrid.html(`
                        <div class="col-12">
                            <div class="text-center text-danger py-5">Failed to load best seller data.</div>
                        </div>
                    `);
                }
            },
        });
    }

    function animateCount(targetId, finalValue) {
        const startValue = parseInt($(targetId).text().replace(/,/g, "")) || 0;

        if (startValue === finalValue) {
            $(targetId).text(finalValue.toLocaleString());
            return;
        }

        $({ Counter: startValue }).animate(
            { Counter: finalValue },
            {
                duration: 1500,
                easing: "swing",
                step: function () {
                    $(targetId).text(Math.ceil(this.Counter).toLocaleString());
                },
                complete: function () {
                    $(targetId).text(Math.ceil(finalValue).toLocaleString());
                },
            }
        );
    }

    fetchAndUpdateCounts();

    function fetchAndUpdateCounts() {
        $.get("/inventory/data-to-display", function (data) {
            if (data) {
                animateCount("#toRecieveCount", data.to_receive || 0);
                animateCount("#totalStocksCount", data.total_stocks || 0);
                animateCount("#lowStocksCount", data.low_stocks || 0);
                animateCount("#outOfStockCount", data.out_of_stock || 0);
                animateCount("#expiredCount", data.expired || 0);
                animateCount("#expiringSoonCount", data.expiring_soon || 0);
                animateCount("#activeItemsCount", data.active_items || 0);
                const totalValue = parseFloat(data.total_value || 0);
                $("#totalValueAmount").text(
                    "₱ " + totalValue.toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                );
                reloadTable("recentItems");
            } else {
                console.error("No data received from the server.");
            }
        }).fail(function (xhr) {
            const errorMsg = xhr.responseJSON
                ? xhr.responseJSON.error
                : "Failed to fetch dashboard counts.";
            console.error(errorMsg);
        });
    }

    const ALERT_LIMIT = 3;

    const ALERT_TO_CLAIM_ID = "#invClaims";
    const ALERT_TO_RESTOCK_ID = "#invRestock";

    let allPurchaseRequests = [];
    let items = [];

    function getToReceiveRequests() {
        fetch(`/inventory/get-to-receive`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        "Network response was not ok: " + response.statusText
                    );
                }
                return response.json();
            })
            .then((response) => {
                allPurchaseRequests = response.data || [];
                $(ALERT_TO_CLAIM_ID).empty();
                buildReceiveAlerts(allPurchaseRequests, ALERT_LIMIT);
            })
            .catch((error) => {
                console.error("Error fetching 'To Receive' data:", error);
                $(ALERT_TO_CLAIM_ID).html(
                    `<div class="alert alert-danger">Error loading pending receipts.</div>`
                );
            });
    }
    function getToRestockItems() {
        fetch(`/inventory/get-to-restock`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(
                        "Network response was not ok: " + response.statusText
                    );
                }
                return response.json();
            })
            .then((response) => {
                items = response.data || [];
                $(ALERT_TO_RESTOCK_ID).empty();
                buildRestockAlerts(items, ALERT_LIMIT);
            })
            .catch((error) => {
                console.error("Error fetching 'To Restock' data:", error);
                $(ALERT_TO_RESTOCK_ID).html(
                    `<div class="alert alert-danger">Error loading pending receipts.</div>`
                );
            });
    }

    function buildReceiveAlerts(requests, limit = requests.length) {
        $(ALERT_TO_CLAIM_ID).empty();

        if (!Array.isArray(requests) || requests.length === 0) {
            $(ALERT_TO_CLAIM_ID).html(
                `<div class="alert alert-light-success">No purchase orders are currently ready for receiving.</div>`
            );
            return;
        }

        const requestsToDisplay = requests.slice(0, limit);
        const hasMore = requests.length > limit;

        requestsToDisplay.forEach((request) => {
            const invoiceId = request.invoice_id;
            const requestId = request.id;
            const supplierName = request.supplier_name || "N/A";

            let totalItemCount = 0;

            if (
                request.purchase_orders &&
                Array.isArray(request.purchase_orders)
            ) {
                request.purchase_orders.forEach((po) => {
                    if (po.details && Array.isArray(po.details)) {
                        totalItemCount += po.details.length;
                    }
                });
            }

            const alertHtml = `
                <div class="alert alert-light-success alert-dismissible fade show" role="alert">
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8 col-md-8 justify-content-start d-flex">
                            <div class="d-block">
                                <h6>PO NO: ${requestId}</h6>
                                <p class="mb-1 ">From: ${supplierName}</p>
                                <p class="mb-0 ">Total Item(s): ${totalItemCount}</p>
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 col-md-4 p-0 justify-content-end align-items-center d-flex">
                            <a href="#" class="btn icon btn-sm btn-success btn-receive bs-tooltip me-2" data-id="${invoiceId}" data-req-id="${requestId}" title="Receive Inventory">
                                <i class="fa-solid fa-box-open"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;

            $(ALERT_TO_CLAIM_ID).append(alertHtml);
        });

        if (hasMore) {
            const remainingCount = requests.length - limit;
            const viewAllHtml = `
                <div class="text-center mt-3" id="viewAllContainer">
                    <a href="#" class="btn btn-sm btn-outline-info btn-show-all">
                        View All ${remainingCount} More Requests
                    </a>
                </div>
            `;
            $(ALERT_TO_CLAIM_ID).append(viewAllHtml);
        }
    }

    function buildRestockAlerts(requests, limit = requests.length) {
        $(ALERT_TO_RESTOCK_ID).empty();

        if (!Array.isArray(requests) || requests.length === 0) {
            $(ALERT_TO_RESTOCK_ID).html(
                `<div class="alert alert-light-warning">No items are currently low in stocks.</div>`
            );
            return;
        }

        const requestsToDisplay = requests.slice(0, limit);
        const hasMore = requests.length > limit;

        requestsToDisplay.forEach((request) => {
            const item_id = request.item_id;
            const sku = request.sku;
            const category = request.category || "N/A";
            const item_name = request.item_name || "N/A";
            const unit_price = request.unit_price;
            const unit =
                request.original_unit ||
                request.unit;
            let head = `<div class="alert alert-light-warning alert-dismissible fade show" role="alert">`;
            const stockRaw = Number(
                request.original_stock_level ?? request.stock_level ?? 0
            );
            const stockDisplay =
                request.original_stock_display ||
                request.stock_display ||
                (request.original_stock_level_formatted ??
                request.stock_level_formatted
                    ? `${request.original_stock_level_formatted ?? request.stock_level_formatted}${
                          unit ? " " + unit : ""
                      }`
                    : Number(stockRaw || 0).toLocaleString("en-PH", {
                          minimumFractionDigits: 2,
                          maximumFractionDigits: 2,
                      }) + (unit ? " " + unit : ""));
            let counts = `<p class="mb-0 ">Current Stock(s): ${stockDisplay}</p>`;
            if (stockRaw === 0) {
                head = `<div class="alert alert-light-danger alert-dismissible fade show" role="alert">`;
                counts = `<p class="mb-0 ">${stockDisplay}</p>`;
            }

            const alertHtml = `
                ${head}
                    <div class="row align-items-center">
                        <div class="col-8 col-lg-8 col-md-8 justify-content-start d-flex">
                            <div class="d-block">
                                <h6>${sku}</h6>
                                <p class="mb-0 ">Category: ${category}</p>
                                <p class="mb-0 ">Item Name: ${item_name}</p>
                                ${counts}
                            </div>
                        </div>
                        <div class="col-4 col-lg-4 col-md-4 p-0 justify-content-end align-items-center d-flex">
                            <a href="#" class="btn icon btn-sm btn-success btn-restock bs-tooltip me-2"
                            data-id="${sku}" data-item-id="${item_id}"
                            data-item-name="${item_name}" data-unit-price="${unit_price}"
                            data-unit="${unit}" title="Restock Inventory">
                                <i class="fa-solid fa-receipt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;

            $(ALERT_TO_RESTOCK_ID).append(alertHtml);
        });

        if (hasMore) {
            const remainingCount = requests.length - limit;
            const viewAllHtml = `
                <div class="text-center mt-3" id="viewAll">
                    <a href="#" class="btn btn-sm btn-outline-info btn-show-restock">
                        View All ${remainingCount} More Requests
                    </a>
                </div>
            `;
            $(ALERT_TO_RESTOCK_ID).append(viewAllHtml);
        }
    }

    $(document).on("click", ".btn-receive", function () {
        const id = $(this).data("id");
        const req_id = $(this).data("req-id");
        $("#LoadingScreen").fadeIn(200);

        $.get(
            `/inventory/items-to-receive/get-invoice/${id}`,
            function (response) {
                if (response.data) {
                    const requestData = response.data;
                    buildInvoiceModal(requestData, req_id);
                } else {
                    alert("Error: Invoice not found.");
                }
            }
        )
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load purchase invoice details.";
                alert(errorMsg);
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    });

    $(document).on("click", ".btn-restock", function () {
        const id = $(this).data("id");
        const item_id = $(this).data("item-id");
        const item_name = $(this).data("item-name");
        const unit_price = $(this).data("unit-price");
        const unit = $(this).data("unit");

        $("#req_item_id").val(item_id);
        $("#req_sku").val(id);
        $("#req_item_name").text("Item Name: " + item_name);
        $("#req_unit_price").text("Unit Price: ₱" + unit_price);
        $("#req_unit").text("Unit: " + unit);
        $("#req_unit_price").attr("data-price", unit_price);
        $("#stockRequest").modal("show");
    });

    function updateTotalPrice() {
        const $unitPriceElement = $("#req_unit_price");
        const unitPrice = parseFloat($unitPriceElement.data("price")) || 0;
        const $quantityInput = $("#qnty");
        const quantity = parseInt($quantityInput.val()) || 0;
        const $totalPriceElement = $("#total_price");

        if (unitPrice <= 0) {
            $totalPriceElement.text("");
            return;
        }

        const totalPriceValue = unitPrice * quantity;

        const formattedPrice = totalPriceValue.toLocaleString("en-PH", {
            style: "currency",
            currency: "PHP",
            minimumFractionDigits: 2,
        });

        $totalPriceElement.text("Total Price: " + formattedPrice);
    }

    $("#cancelStockReq").click(function (e) {
        e.preventDefault();
        clearRestockFields();
    });

    $("#qnty").on("input", updateTotalPrice);

    updateTotalPrice();

    $("#submit-req-btn").click(function (e) {
        e.preventDefault();
        let isValid = true;
        const $form = $("#restockReqForm");

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
            let formData = new FormData($("#restockReqForm")[0]);
            Swal.fire({
                title: "Confirm Request",
                text: "You are about to request a restock for this item.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Submit",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#dc3545",
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#LoadingScreen").fadeIn(200);
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        url: "/inventory/send-restock-request",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            $("#LoadingScreen").fadeOut(200);
                            $("#restockReqForm").trigger("reset");
                            Toast.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                            });
                            fetchAndUpdateCounts();
                            getToReceiveRequests();
                            getToRestockItems();
                            clearRestockFields();
                            $("#stockRequest").modal("hide");
                        },
                        error: function (xhr) {
                            // console.error('Error response:', xhr);
                            $("#LoadingScreen").fadeOut(200);
                            if (xhr.responseJSON?.errors) {
                                let errorMessages = Object.values(
                                    xhr.responseJSON.errors
                                )
                                    .flat()
                                    .join("\n");
                                Toast.fire(
                                    "Validation Error",
                                    errorMessages,
                                    "error"
                                );
                            } else {
                                Toast.fire(
                                    "Error",
                                    "An unexpected error occurred.",
                                    "error"
                                );
                            }
                        },
                    });
                }
            });
        }
    });

    function clearRestockFields() {
        $("#req_item_id").val("");
        $("#req_sku").val("");
        $("#qnty").val("");
        $("#req_item_name").text("");
        $("#req_unit_price").text("");
        $("#total_price").text("");
        $("#req_unit_price").removeAttr("data-price");
    }

    $(document).on("click", "#receiveItem", function () {
        const req_id = $(this).data("id");
        Swal.fire({
            title: "Confirm Receipt",
            text: "Are you sure you want to mark this purchase order as received? This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Receive",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#dc3545",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);
                $.post(
                    `/inventory/items-to-receive/receive-inventory/${req_id}`,
                    { _token: $('meta[name="csrf-token"]').attr("content") },
                    function (response) {
                        if (response.success) {
                            Toast.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                            });
                            fetchAndUpdateCounts();
                            getToReceiveRequests();
                            getToRestockItems();
                            $("#viewInvoice").modal("hide");
                        } else {
                            const errorMsg =
                                response.error ||
                                "Failed to mark items as received.";
                            Swal.fire("Error", errorMsg, "error");
                        }
                    }
                )
                .fail(function (xhr) {
                    const errorMsg = xhr.responseJSON
                        ? xhr.responseJSON.error
                        : "Failed to mark items as received.";
                    Swal.fire("Error", errorMsg, "error");
                })
                .always(function () {
                    $("#LoadingScreen").fadeOut(200);
                });
            }
        });
    });

    $(document).on("click", ".btn-show-all", function (e) {
        e.preventDefault();
        if (allPurchaseRequests.length > 0) {
            buildReceiveAlerts(allPurchaseRequests, allPurchaseRequests.length);
        } else {
            console.error("Full request list is not available.");
        }
    });
    $(document).on("click", ".btn-show-restock", function (e) {
        e.preventDefault();
        if (items.length > 0) {
            buildRestockAlerts(items, items.length);
        } else {
            console.error("Full request list is not available.");
        }
    });

    if (hasBestSellerSection) {
        $(document).on("click", ".best-seller-summary-card", function () {
            const $card = $(this);
            if ($card.attr("data-has-data") !== "true") {
                return;
            }
            const mode = $card.data("mode");
            openBestSellerOverlay(mode);
        });

        $bestSellerOverlay.on("click", function (event) {
            if ($(event.target).is($bestSellerOverlay)) {
                closeBestSellerOverlay();
            }
        });

        $(document).on("click", "[data-overlay-dismiss]", function (event) {
            event.preventDefault();
            closeBestSellerOverlay();
        });

        $(document).on("keydown", function (event) {
            if (
                event.key === "Escape" &&
                $bestSellerOverlay.hasClass("active")
            ) {
                closeBestSellerOverlay();
            }
        });
    }

    getToReceiveRequests();
    getToRestockItems();
    if (hasBestSellerSection) {
        loadBestSellers();
    }

    $("#btn-refresh-recent-items").on("click", function () {
        fetchAndUpdateCounts();
        getToReceiveRequests();
        getToRestockItems();
        if (hasBestSellerSection) {
            loadBestSellers();
        }
        recentItemsTable.ajax.reload(null, false);
    });

    function buildInvoiceModal(data, req_id) {
        const po = new Set();
        let allDetailRowsHtml = "";
        let itemIndex = 0;

        if (data.purchase_orders && data.purchase_orders.length > 0) {
            data.purchase_orders.forEach((order) => {
                po.add(order.purchase_order_id || "N/A");

                const details = order.details || [];

                if (details.length > 0) {
                    details.forEach((item) => {
                        itemIndex++;
                        allDetailRowsHtml += `
                    <tr>
                        <td>${itemIndex}</td>
                        <td>${item.item_name || "N/A"}</td>
                        <td>${item.item_unit_name || "N/A"}</td>
                        <td class="text-end">₱${parseFloat(
                            item.unit_price || 0
                        ).toFixed(2)}</td>
                        <td class="text-end">${item.quantity || 0} ${
                            item.item_unit || "N/A"
                        }</td>
                        <td class="text-end">₱${parseFloat(
                            item.total_amount || 0
                        ).toFixed(2)}</td>
                    </tr>
                    `;
                    });
                }
            });
        }

        const poNum = Array.from(po).join(", ");

        if (allDetailRowsHtml === "") {
            allDetailRowsHtml = `<tr><td colspan="7" class="text-center">No item details were found across all Purchase Orders.</td></tr>`;
        }

        const html = `
    <div class="row mb-4 p-3">
        <div class="col-md-6">
            <p class="mb-0">Purchase Order #: ${poNum || "N/A"}</p>
            <p class="mb-0">Supplier: ${data.supplier_name}</p>
            <p class="mb-0">Date Approved: ${data.date_approved || "N/A"}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <p class="mb-0">Invoice #: ${data.id || "N/A"}</p>
            <p class="mb-0">Delivery #: ${data.delivery_no || "N/A"}</p>
            <p class="mb-0">Delivered On: ${data.date_received || "N/A"}</p>
        </div>
    </div>

    <hr class="mt-0">

    <div class="px-3">
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover dataTable no-footer">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    ${allDetailRowsHtml}
                    <tr>
                        <td colspan="5" class="text-end"><strong>Total Amount:</strong></td>
                        <td class="text-end"><strong>₱${parseFloat(
                            data.total_amount || 0
                        ).toFixed(2)}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <style>
    .table-sm td,
    .table-sm th {
        padding: 0.4rem 0.6rem;
        font-size: 0.875rem;
    }
    </style>
    `;
        $("#receiveItem").data("id", req_id);
        $("#LoadingScreen").fadeOut(200);
        $("#viewInvoice .modal-body").html(html);
        $("#viewInvoice").modal("show");
    }
});
