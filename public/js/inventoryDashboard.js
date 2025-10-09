import { reloadTable } from "./utils/reloadTable.js";

$(document).ready(function () {
    $("#recentItems").DataTable({
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
                width: "45px",
            },
            { data: "sku", className: "dt-left" },
            { data: "item_name", className: "dt-left" },
            { data: "unit", className: "dt-left" },
            { data: "category", className: "dt-left" },
            { data: "stock_level", className: "dt-left" },
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
    });

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
                    $(targetId).text(finalValue.toLocaleString());
                },
            }
        );
    }

    function fetchAndUpdateCounts() {
        $.get("/inventory/data-to-display", function (data) {
            if (data) {
                animateCount("#toRecieveCount", data.to_receive || 0);
                animateCount("#totalStocksCount", data.total_stocks || 0);
                animateCount("#lowStocksCount", data.low_stocks || 0);
                animateCount("#outOfStockCount", data.out_of_stock || 0);
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

    fetchAndUpdateCounts();

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

    /**
     * Builds and inserts HTML alerts for pending Purchase Requests.
     * @param {Array<Object>} requests - Array of Purchase Request objects.
     * @param {number} [limit=requests.length] - The maximum number of requests to display.
     */
    function buildReceiveAlerts(requests, limit = requests.length) {
        // Clear the container before rendering
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

        // Add the "View All" link if more items are available
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
            const unit = request.unit;
            let head = `<div class="alert alert-light-warning alert-dismissible fade show" role="alert">`;
            let counts = `<p class="mb-0 ">Current Stock(s): ${request.stock_level}</p>`;
            if (request.stock_level === 0) {
                head = `<div class="alert alert-light-danger alert-dismissible fade show" role="alert">`;
                counts = `<p class="mb-0 ">${request.stock_level} Stock</p>`;
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
        $("#req_unit_price").text("Unit Pirce: ₱" + unit_price);
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
                    function (response) {
                        if (response.success) {
                            Toast.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                            });
                            // Refresh counts and alerts
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

    getToReceiveRequests();
    getToRestockItems();

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
            allDetailRowsHtml = `<tr><td colspan="8" class="text-center">No item details were found across all Purchase Orders.</td></tr>`;
        }

        const html = `
    <div class="row mb-4 p-3">
        <!-- Invoice Header -->
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
                        <td colspan="5" class="text-end"><strong>₱${parseFloat(
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
