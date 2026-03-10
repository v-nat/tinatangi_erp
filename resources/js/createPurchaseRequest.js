import $ from 'jquery';
import Swal from 'sweetalert2';
import {
    buildInvoiceModal,
    buildPOmodal,
    printInvoice,
} from "./utils/modal_builders_scm.js";
import { reloadTable } from "./utils/reloadTable.js";
import { formatDate } from "./utils/formatDateAndTime.js";

$(document).ready(function () {
    $("#createPR").on("click", function (e) {
        e.preventDefault();
        generateOrderId();
        $("#createPrModal").modal("show");
    });

    function generateOrderId() {
        fetch(`/procurement/generateOrderID/${"purchase_order"}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                $("#order_id").val(data.order_id);
            })
            .catch((error) => {
                console.error("Error fetching Order ID:", error);
            });
    }

    $(document).on("click", ".close-btn", function () {
        var allTableData = orderTable.rows().data().toArray();
        var cleanedData = allTableData.map(function (item) {
            return {
                item_id: parseInt(item.item_id),
                supplier_id: parseInt(item.supplier_id),
                category_id: parseInt(item.category_id),
                qnty: item.qnty,
                unit_price: parseFloat(item.unit_price),
                total: parseFloat(item.total),
            };
        });
        if (!(cleanedData.length === 0)) {
            Swal.fire({
                title: "Close Form?",
                text: "All the data you input will not be saved",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirm",
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }
                $("#category").val("");
                // Clear item fields after confirmation
                itemSearchInput.value = "";
                itemHiddenInput.value = "";
                availableItems = [];
                itemResultsContainer.classList.add("d-none");
                $("order_id").val("");
                $("#supplier").val("");
                $("#unit").val("");
                $("#unit_price").val("");
                $("#qnty").val("");

                $("#category").prop("disabled", false);
                $("#item_search_input").prop("readonly", false);
                $("#qnty").prop("readonly", false);
                $("#addItem").prop("disabled", false);

                $("#submit-PO").parent("div").removeClass("d-none");
                $("#submit-PR").parent("div").addClass("d-none");

                orderTable.clear().draw();
                $("#createPrModal").modal("hide");
            });
        } else {
            $("#createPrModal").modal("hide");
        }
    });

    $(document).on("click", ".complete-req-btn", function () {
        Swal.fire({
            title: "Complete Request?",
            text: "Modify the needed data to put this request on process",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);

            const id = $(this).data("id");

            $.get(
                `/procurement/purchases/get-restock-data/${id}`,
                function (response) {
                    if (response.data && response.data.id) {
                        $("#order_id").val(id);
                        initializePrForm(response);
                    } else {
                        alert("Error: Purchase Request not found.");
                    }
                }
            )
                .fail(function (xhr) {
                    const errorMsg = xhr.responseJSON
                        ? xhr.responseJSON.error
                        : "Failed to load purchase request details.";
                    alert(errorMsg);
                })
                .always(function () {
                    $("#LoadingScreen").fadeOut(200);
                });
        });
    });
    $(document).on("click", ".btn-view", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/procurement/purchases/get-details/${id}`, function (response) {
            if (response.data && response.data.length > 0) {
                const requestData = response.data[0];
                buildPOmodal(requestData);
            } else {
                alert("Error: Purchase Request not found.");
            }
        })
            .fail(function (xhr) {
                const errorMsg = xhr.responseJSON
                    ? xhr.responseJSON.error
                    : "Failed to load purchase request details.";
                alert(errorMsg);
            })
            .always(function () {
                $("#LoadingScreen").fadeOut(200);
            });
    });

    $(document).on("click", ".btn-invoice", function () {
        const id = $(this).data("id");
        $("#LoadingScreen").fadeIn(200);

        $.get(`/procurement/purchases/get-invoice/${id}`, function (response) {
            if (response.data) {
                const requestData = response.data;
                buildInvoiceModal(requestData);
            } else {
                alert("Error: Invoice not found.");
            }
        })
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

    $(document).on("click", "#print", function () {
        printInvoice();
    });

    function initializePrForm(data) {
        const requestData = data.data || data;
        if (typeof orderTable !== "undefined") {
            orderTable.clear().draw();
        } else {
            console.error("orderTable is not defined. Cannot clear table.");
            return;
        }

        formMode = FORM_MODES.COMPLETE_REQUEST;

        $("#category").prop("disabled", true);
        $("#item_search_input").prop("readonly", true);
        $("#qnty").prop("readonly", true);
        $("#addItem").prop("disabled", true);

        $("#submit-PO").parent("div").addClass("d-none");
        $("#submit-PR").parent("div").removeClass("d-none");

        const supplierIdsFromData = new Set();
        const supplierNamesFromData = new Set();

        $("#unit").val("");
        $("#unit_price").val("");

        // --- POPULATE TABLE ---
        if (requestData) {
            if (
                Array.isArray(requestData.purchase_orders) &&
                requestData.purchase_orders.length > 0
            ) {
                requestData.purchase_orders.forEach((order) => {
                    const details = order.details || [];
                    const orderSupplierId =
                        order.supplier_id ?? requestData.supplier_id ?? "";
                    const orderSupplierName =
                        order.supplier_name ?? requestData.supplier_name ?? "";

                    if (orderSupplierId) {
                        supplierIdsFromData.add(String(orderSupplierId));
                    }
                    if (orderSupplierName) {
                        supplierNamesFromData.add(orderSupplierName);
                    }

                    if (Array.isArray(details) && details.length > 0) {
                        details.forEach((item) => {
                            const orderId = order.purchase_order_id;
                            const itemSupplierId =
                                item.supplier_id ?? orderSupplierId ?? "";
                            const supplierName =
                                item.supplier_name ??
                                orderSupplierName ??
                                "";

                            if (itemSupplierId) {
                                supplierIdsFromData.add(
                                    String(itemSupplierId)
                                );
                            }
                            if (supplierName) {
                                supplierNamesFromData.add(supplierName);
                            }

                            const newRowData = {
                                supplier: supplierName,
                                supplier_id: itemSupplierId,
                                order_id: orderId,
                                category: item.category_name,
                                item: item.item_name,
                                item_id: item.item_id,
                                category_id: item.category_id,
                                unit: item.item_unit,
                                qnty: item.quantity,
                                unit_price: item.unit_price
                                    ? item.unit_price.toFixed(2)
                                    : "0.00",
                                total: item.total_amount
                                    ? item.total_amount.toFixed(2)
                                    : "0.00",
                                action: ``,
                            };

                            orderTable.row.add(newRowData);
                        });
                    }
                });

                orderTable.draw();
            }
        }

        itemSearchInput.value = "";
        itemHiddenInput.value = "";
        availableItems = [];
        itemResultsContainer.classList.add("d-none");

        const supplierConfig = {};
        if (supplierIdsFromData.size > 0) {
            supplierConfig.allowedIds = supplierIdsFromData;
            if (supplierIdsFromData.size === 1) {
                supplierConfig.defaultValue = Array.from(
                    supplierIdsFromData
                )[0];
                supplierConfig.disableWhenSingle = true;
            }
        } else if (supplierNamesFromData.size > 0) {
            supplierConfig.allowedNames = supplierNamesFromData;
            supplierConfig.disableWhenSingle =
                supplierNamesFromData.size === 1;
        } else {
            supplierConfig.disableWhenSingle = false;
        }

        if (requestData && requestData.supplier_id) {
            const supplierIdString = String(requestData.supplier_id);
            supplierIdsFromData.add(supplierIdString);
            supplierConfig.allowedIds = supplierIdsFromData;
            supplierConfig.defaultValue = supplierIdString;
            supplierConfig.disableWhenSingle = true;
        } else if (requestData && requestData.purchase_orders) {
            const firstOrderWithSupplier = requestData.purchase_orders.find(
                (order) => order.supplier_id
            );
            if (firstOrderWithSupplier && firstOrderWithSupplier.supplier_id) {
                const supplierIdString = String(
                    firstOrderWithSupplier.supplier_id
                );
                supplierIdsFromData.add(supplierIdString);
                supplierConfig.allowedIds = supplierIdsFromData;
                supplierConfig.defaultValue = supplierIdString;
            }
        }

        configureSupplierOptions(supplierConfig);
        getSuppliers();

        $("#createPrModal").modal("show");
    }


    const purchaseRequestTable = $("#purchaseRequestTable").DataTable({
        responsive: true,
        scrollX: false,
        processing: true,
        serverSide: false,
        ajax: {
            url: "/procurement/purchases/get-requests-list",
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
            {
                data: "purchase_orders",
                title: "Order No.",
                className: "dt-left",
                render: function (data) {
                    if (data && data.length > 0) {
                        return data[0].purchase_order_id;
                    }
                    return "N/A";
                },
                className: "dt-left",
            },
            { data: "type", className: "dt-left" },
            {
                data: "purchase_orders",
                title: "Order Date",
                className: "dt-left",
                render: function (data, type, row) {
                    let orderDate = "N/A";
                    if (data && data.length > 0 && data[0].order_date) {
                        orderDate = data[0].order_date;
                    }

                    if (type === 'display') {
                        return orderDate !== "N/A" ? formatDate(orderDate) : "N/A";
                    }
                    return orderDate;
                },
                className: "dt-left",
            },
            {
                data: "purchase_orders",
                title: "Supplier",
                className: "dt-left",
                render: function (data) {
                    if (data && data.length > 0) {
                        return data[0].supplier_name;
                    }
                    return "N/A";
                },
                className: "dt-left",
            },
            {
                data: "purchase_orders",
                title: "Delivery Date",
                render: function (data) {
                    if (data && data.length > 0) {
                        const deliveryDate = data[0].delivery_date;
                        return deliveryDate ? formatDate(deliveryDate) : "N/A";
                    }
                    return "N/A";
                },
                type: "date",
                className: "dt-left",
            },
            { data: "requested_by_id", className: "dt-left" },
            { data: "remarks", className: "dt-left" },
            {
                data: "status",
                className: "text-center",
                width: "150px",
            },
            {
                data: "id",
                render: function (data, type, row) {
                    let invoice_id = null;
                    if (row.invoice_id) {
                        invoice_id = row.invoice_id;
                    }
                    if (
                        row.status ==
                        '<span class="badge bg-warning">Pending</span>'
                    ) {
                        return `
                            <div class="action-btns">
                                <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                                data-id="${data}"
                                title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                            `;
                    } else if (
                        row.status ==
                            '<span class="badge bg-success">Delivered</span>' ||
                        row.status ==
                            '<span class="badge bg-success">Completed</span>'
                    ) {
                        return `
                            <div class="action-btns">
                                <a href="#" class="btn icon btn-sm btn-info btn-invoice bs-tooltip me-2"
                                data-id="${invoice_id}"
                                title="Invoice">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                            `;
                    } else if (
                        row.status ==
                        '<span class="badge bg-warning">Pending<br>Restock</span>'
                    ) {
                        return `
                            <div class="action-btns">
                                <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                                data-id="${data}"
                                title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="#" class="btn icon btn-sm btn-primary bs-tooltip me-2 complete-req-btn"
                                    data-id="${data}"
                                    title="Process">
                                        <i class="fa-solid fa-receipt"></i>
                                </a>
                            </div>
                            `;
                    } else {
                        return `
                            <div class="action-btns">
                                <a href="#" class="btn icon btn-sm btn-info btn-view bs-tooltip me-2"
                                data-id="${data}"
                                title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                            `;
                    }
                },
                className: "text-center",
                width: "150px",
            },
            {
                data: "invoice_id",
                visible: false,
            },
        ],
        initComplete: function () {
            const typeColumn = this.api().column(2);
            const typeSelect = $('#pr_type_filter');

            const supplierColumn = this.api().column(4);
            const supplierSelect = $('#pr_supplier_filter');

            typeColumn.data().unique().sort().each(function (d, j) {
                if(d) {
                    typeSelect.append($('<option></option>').attr('value', d).text(d));
                }
            });

            const supplierNames = new Set();
            supplierColumn.data().each(function (d, j) {
                let supplierName = "N/A";
                if (d && d.length > 0 && d[0].supplier_name) {
                    supplierName = d[0].supplier_name;
                }
                supplierNames.add(supplierName);
            });

            const sortedNames = Array.from(supplierNames).sort();
            sortedNames.forEach(function(name) {
                supplierSelect.append($('<option></option>').attr('value', name).text(name));
            });

            const statusColumn = this.api().column(8);
            const statusSelect = $("#status_filter");
            const statusValues = new Set();

            statusColumn
                .data()
                .unique()
                .each(function (d, j) {
                    if (d) {
                        let statusText = $(d).text();
                        if (!statusText) statusText = d;

                        statusValues.add(statusText);
                    }
                });
            const sortedStatuses = Array.from(statusValues).sort();

            sortedStatuses.forEach(function (text) {
                statusSelect.append(
                    $("<option></option>").attr("value", text).text(text)
                );
            });
        }
    });


    $("#pr_type_filter").on("change", function() {
        const selectedType = $(this).val();
        purchaseRequestTable.column(2).search(
            selectedType ? '^' + selectedType + '$' : '',
            true,
            false
        ).draw();
    });

    $("#pr_order_date_filter").on("change", function() {
        const selectedDate = $(this).val();
        purchaseRequestTable.column(3).search(selectedDate).draw();
    });

    $("#pr_supplier_filter").on("change", function() {
        const selectedSupplier = $(this).val();
        purchaseRequestTable.column(4).search(
            selectedSupplier ? '^' + selectedSupplier + '$' : '',
            true,
            false
        ).draw();
    });

    $("#status_filter").on("change", function () {
        const selectedStatus = $(this).val();
        purchaseOrderTable
            .column(8)
            .search(selectedStatus, false, false)
            .draw();
    });

    $("#btn-refresh-purchase-requests").on("click", function () {
        purchaseRequestTable.ajax.reload(null, false);
    });

    var orderTable = $("#orderRequest").DataTable({
        columns: [
            {
                data: "null",
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
            },
            {
                data: "item",
                className: "dt-left",
                width: "300px",
            },
            {
                data: "unit",
                className: "dt-left",
            },
            {
                data: "qnty",
                className: "dt-left",
            },
            {
                data: "unit_price",
                className: "dt-left",
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },
            {
                data: "total",
                className: "dt-left",
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },

            {
                data: "action",
                width: "150px",
            },
            {
                data: "order_id",
                visible: false,
            },
            {
                data: "item_id",
                visible: false,
            },
            {
                data: "category_id",
                visible: false,
            },
            {
                data: "supplier",
                visible: false,
            },
            {
                data: "supplier_id",
                visible: false,
            },
        ],
    });

    function clearItemFormFields() {
        $("#category").val("");
        itemSearchInput.value = "";
        itemHiddenInput.value = "";
        availableItems = [];
        itemResultsContainer.classList.add("d-none");
        $("#unit").val("");
        $("#unit_price").val("");
        $("#qnty").val("");
    }

    function isSupplierCompatibleWithItems(supplierId) {
        if (!supplierId || typeof orderTable === "undefined") {
            return true;
        }

        const rows = orderTable.rows().data().toArray();
        if (!rows.length) {
            return true;
        }

        const mismatch = rows.find((row) => {
            if (
                row.supplier_id === null ||
                row.supplier_id === undefined ||
                row.supplier_id === ""
            ) {
                return false;
            }
            return String(row.supplier_id) !== String(supplierId);
        });

        if (mismatch) {
            Toast.fire({
                title: "Supplier Mismatch",
                text: "Some items are assigned to a different supplier.",
                icon: "warning",
            });
            return false;
        }

        return true;
    }

    function resetModalState() {
        formMode = FORM_MODES.CREATE;
        resetSupplierOptions();
        suppressSupplierChange = true;
        $("#supplier").val("");
        suppressSupplierChange = false;
        selectedSupplierId = null;
        previousSupplierId = null;

        $("#supplier").prop("disabled", false);
        $("#category").prop("disabled", false);
        $("#item_search_input").prop("readonly", false);
        $("#qnty").prop("readonly", false);
        $("#addItem").prop("disabled", false);

        $("#submit-PO").parent("div").removeClass("d-none");
        $("#submit-PR").parent("div").addClass("d-none");

        if (typeof orderTable !== "undefined") {
            orderTable.clear().draw();
        }

        clearItemFormFields();
        getSuppliers();
        setTimeout(() => {
            loadCategories();
        }, 0);
    }

    $("#createPrModal").on("hidden.bs.modal", function () {
        resetModalState();
    });

    const itemSearchInput = document.getElementById("item_search_input");
    const itemHiddenInput = document.getElementById("item");
    const itemResultsContainer = document.getElementById(
        "item_results_container"
    );
    const itemSearchList = document.getElementById("item_search_list");
    let availableItems = [];
    let selectedSupplierId = null;
    let previousSupplierId = null;

    const FORM_MODES = {
        CREATE: "create",
        COMPLETE_REQUEST: "complete_request",
    };

    let formMode = FORM_MODES.CREATE;
    let supplierOptionsConfig = {
        allowedIds: null,
        allowedNames: null,
        defaultValue: null,
        disableWhenSingle: false,
    };
    let suppressSupplierChange = false;

    function configureSupplierOptions({
        allowedIds = null,
        allowedNames = null,
        defaultValue = null,
        disableWhenSingle = false,
    } = {}) {
        supplierOptionsConfig = {
            allowedIds: allowedIds
                ? new Set(
                      Array.from(allowedIds).map((value) => String(value))
                  )
                : null,
            allowedNames: allowedNames
                ? new Set(Array.from(allowedNames))
                : null,
            defaultValue:
                defaultValue !== null && defaultValue !== undefined
                    ? String(defaultValue)
                    : null,
            disableWhenSingle: Boolean(disableWhenSingle),
        };
    }

    function resetSupplierOptions() {
        configureSupplierOptions();
    }

    function loadCategories(supplierId = null) {
        let url = `/procurement/create-purchase-request/get-categories`;
        if (supplierId) {
            url += `?supplier=${encodeURIComponent(supplierId)}`;
        }

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                categorySelect.innerHTML =
                    '<option value="" disabled selected>Choose Category</option>';
                data.forEach((category) => {
                    const option = document.createElement("option");
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });
                $(categorySelect).prop("disabled", data.length === 0);
                if (data.length === 0) {
                    Toast.fire({
                        title: "Notice",
                        text: "No categories available for the selected supplier.",
                        icon: "info",
                    });
                }
            })
            .catch(() => {
                Toast.fire({
                    title: "Error",
                    text: "Unable to load categories.",
                    icon: "error",
                });
            });
    }

    /**
     * Handles item selection from the dropdown list.
     * @param {Object} item - The selected item object.
     */
    function handleItemSelection(item) {
        itemSearchInput.value = item.name;
        itemHiddenInput.value = item.id;

        $("#unit").val(item.unit_label || item.unit_id || "");
        $("#unit_price").val(item.unit_price);

        itemResultsContainer.classList.add("d-none");

        $(itemSearchInput).removeClass("is-invalid");
    }

    /**
     * Renders the filtered list of items in the dropdown.
     * @param {Array<Object>} filteredItems - The items to display.
     */
    function renderItems(filteredItems) {
        itemSearchList.innerHTML = "";
        if (filteredItems.length === 0) {
            const noResult = document.createElement("a");
            noResult.className =
                "list-group-item list-group-item-light small text-muted";
            noResult.textContent = "No items found.";
            itemSearchList.appendChild(noResult);
        } else {
            filteredItems.forEach((item) => {
                const itemLink = document.createElement("a");
                itemLink.className =
                    "list-group-item list-group-item-action py-2 small";
                itemLink.href = "#";
                itemLink.textContent = item.name;
                itemLink.dataset.itemId = item.id;

                itemLink.addEventListener("click", (e) => {
                    e.preventDefault();
                    handleItemSelection(item);
                });
                itemSearchList.appendChild(itemLink);
            });
        }
        itemResultsContainer.classList.remove("d-none");
    }

    $(itemSearchInput).on("input", function () {
        const query = $(this).val().toLowerCase();

        itemHiddenInput.value = "";
        $("#unit").val("");
        $("#unit_price").val("");

        if (query.length === 0) {
            renderItems(availableItems);
            return;
        }

        const filtered = availableItems.filter((item) =>
            item.name.toLowerCase().includes(query)
        );
        renderItems(filtered);
    });

    $(itemSearchInput).on("focus", function () {
        if (availableItems.length > 0) {
            const query = $(this).val().toLowerCase();
            const filtered = availableItems.filter((item) =>
                item.name.toLowerCase().includes(query)
            );
            renderItems(filtered);
        }
    });

    $(document).on("click", function (e) {
        const parentGroup = itemSearchInput.closest(".form-group");
        if (parentGroup && !parentGroup.contains(e.target)) {
            itemResultsContainer.classList.add("d-none");

            const currentName = itemSearchInput.value.trim();
            const matchedItem = availableItems.find(
                (item) => item.name === currentName
            );

            if (currentName !== "" && !matchedItem) {
                itemSearchInput.value = "";
                itemHiddenInput.value = "";
            }
        }
    });

    const categorySelect = document.getElementById("category");

    $("#category").change(function () {
        $("#unit").val("");
        $("#unit_price").val("");
        itemSearchInput.value = "";
        itemHiddenInput.value = "";
        availableItems = [];
        itemResultsContainer.classList.add("d-none");

        const category = categorySelect.value;
        if (category) {
            const supplierId = $("#supplier").val();
            if (!supplierId) {
                Toast.fire({
                    title: "Select Supplier",
                    text: "Please choose a supplier before selecting items.",
                    icon: "warning",
                });
                $("#category").val("");
                return;
            }
            fetch(
                `/procurement/create-purchase-request/get-items?category=${encodeURIComponent(
                    category
                )}&supplier=${encodeURIComponent(supplierId)}`
            )
                .then((res) => res.json())
                .then((data) => {
                    availableItems = data.map((p) => ({
                        id: p.id,
                        name: p.name,
                        unit_id: p.unit_id,
                        unit_label: p.unit_label,
                        unit_price: p.unit_price,
                        supplier_id: p.supplier_id,
                    }));

                    renderItems(availableItems);
                })
                .catch((error) => {
                    console.error("Error fetching items:", error);
                    Toast.fire(
                        "Error",
                        "Could not load items for the selected category.",
                        "error"
                    );
                    availableItems = [];
                });
        }
    });

    function getSuppliers() {
        const supplierSelect = document.getElementById("supplier");

        let {
            allowedIds,
            allowedNames,
            defaultValue,
            disableWhenSingle,
        } = supplierOptionsConfig;

        const isRestockMode = formMode === FORM_MODES.COMPLETE_REQUEST;
        let restockSupplierId = null;

        if (isRestockMode && typeof orderTable !== "undefined") {
            const tableSupplierIds = new Set();
            orderTable
                .rows()
                .data()
                .toArray()
                .forEach((row) => {
                    if (
                        row &&
                        row.supplier_id !== null &&
                        row.supplier_id !== undefined &&
                        row.supplier_id !== ""
                    ) {
                        const supplierId = String(row.supplier_id);
                        tableSupplierIds.add(supplierId);
                        if (!restockSupplierId) {
                            restockSupplierId = supplierId;
                        }
                    }
                });

            if (tableSupplierIds.size > 0) {
                allowedIds = tableSupplierIds;
                allowedNames = null;
                defaultValue = restockSupplierId ?? defaultValue;
                disableWhenSingle = true;
            }
        }

        const allowedIdSet = allowedIds
            ? new Set(Array.from(allowedIds).map(String))
            : null;
        const allowedNameSet = allowedNames
            ? new Set(Array.from(allowedNames))
            : null;

        let url = `/procurement/create-purchase-request/get-active-supplier`;
        const params = new URLSearchParams();
        if (allowedIdSet && allowedIdSet.size > 0) {
            params.set("supplier_ids", Array.from(allowedIdSet).join(","));
        } else if (allowedNameSet && allowedNameSet.size > 0) {
            params.set("supplier_names", Array.from(allowedNameSet).join(","));
        }
        if (params.toString()) {
            url += `?${params.toString()}`;
        }

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                supplierSelect.innerHTML = "";

                const placeholderOption = document.createElement("option");
                placeholderOption.value = "";
                placeholderOption.disabled = true;
                placeholderOption.selected = true;
                placeholderOption.textContent = "Choose...";
                supplierSelect.appendChild(placeholderOption);

                let appendedCount = 0;

                data.forEach((s) => {
                    const id = String(s.id);
                    const name = s.supplier_name || s.name || "";
                    if (allowedIdSet && allowedIdSet.size > 0 && !allowedIdSet.has(id)) {
                        return;
                    }
                    if (allowedNameSet && allowedNameSet.size > 0 && !allowedNameSet.has(name)) {
                        return;
                    }
                    const option = document.createElement("option");
                    option.value = id;
                    option.textContent = name;
                    supplierSelect.appendChild(option);
                    appendedCount += 1;
                });

                if (!isRestockMode && appendedCount === 0) {
                    data.forEach((s) => {
                        const option = document.createElement("option");
                        option.value = String(s.id);
                        option.textContent = s.supplier_name || s.name || "";
                        supplierSelect.appendChild(option);
                    });
                    appendedCount = data.length;
                }

                let nextValue = null;

                if (
                    defaultValue &&
                    Array.from(supplierSelect.options).some(
                        (opt) => opt.value === defaultValue
                    )
                ) {
                    nextValue = defaultValue;
                } else if (
                    allowedIdSet &&
                    allowedIdSet.size === 1 &&
                    Array.from(supplierSelect.options).some((opt) =>
                        allowedIdSet.has(opt.value)
                    )
                ) {
                    nextValue = Array.from(allowedIdSet)[0];
                } else if (
                    (!allowedIdSet || allowedIdSet.size === 0) &&
                    allowedNameSet &&
                    allowedNameSet.size === 1
                ) {
                    const targetName = Array.from(allowedNameSet)[0];
                    const matchedOption = Array.from(
                        supplierSelect.options
                    ).find((opt) => opt.text === targetName);
                    if (matchedOption) {
                        nextValue = matchedOption.value;
                    }
                }

                suppressSupplierChange = true;
                if (nextValue) {
                    supplierSelect.value = nextValue;
                    selectedSupplierId = nextValue;
                    previousSupplierId = nextValue;
                } else {
                    supplierSelect.value = "";
                    selectedSupplierId = null;
                    previousSupplierId = null;
                }
                suppressSupplierChange = false;

                if (isRestockMode) {
                    $("#supplier").prop("disabled", true);
                } else if (disableWhenSingle && appendedCount === 1) {
                    $("#supplier").prop("disabled", true);
                } else {
                    $("#supplier").prop("disabled", false);
                }
            });
    }

    $("#supplier").on("focus", function () {
        previousSupplierId = $(this).val();
    });

    $("#supplier").change(function () {
        if (suppressSupplierChange) {
            const currentValue = $(this).val();
            selectedSupplierId = currentValue;
            previousSupplierId = currentValue;
            return;
        }

        const newSupplierId = $(this).val();

        if (formMode === FORM_MODES.COMPLETE_REQUEST) {
            if (newSupplierId && !isSupplierCompatibleWithItems(newSupplierId)) {
                suppressSupplierChange = true;
                $(this).val(previousSupplierId || "");
                suppressSupplierChange = false;
                return;
            }
            selectedSupplierId = newSupplierId;
            previousSupplierId = newSupplierId;
            return;
        }

        const resetFormForSupplier = () => {
            selectedSupplierId = newSupplierId;
            loadCategories(selectedSupplierId);
            clearItemFormFields();
            orderTable.clear().draw();
        };

        if (orderTable.rows().count() > 0) {
            const firstRowData = orderTable.row(0).data();

            if (firstRowData.supplier_id) {
                Swal.fire({
                    title: "Change Supplier?",
                    text: "Changing Supplier will reset all the fields",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm",
                }).then((result) => {
                    if (!result.isConfirmed) {
                        $("#supplier").val(previousSupplierId);
                        return;
                    }
                    resetFormForSupplier();
                    previousSupplierId = newSupplierId;
                });
                return;
            }
        }
        resetFormForSupplier();
        previousSupplierId = newSupplierId;
    });

    $(document).ready(function () {
        loadCategories();
    });

    $(document).ready(function () {
        const nameInput = document.getElementById("order_id");
        categorySelect.addEventListener("change", clearInvalids);
        nameInput.addEventListener("change", clearInvalids);
        itemSearchInput.addEventListener("change", clearInvalids);
    });

    function clearInvalids() {
        const $form = $("#submitPORequest");
        $form.find("input, select").each(function () {
            const $field = $(this);
            $field.removeClass("is-invalid");
        });
    }

    getSuppliers();

    $("#addItem").on("click", function (e) {
        e.preventDefault();

        let isValid = true;
        const $form = $("#submitPORequest");

        $form.find("input[required], select[required]").each(function () {
            const $field = $(this);
            const value = $field.val();

            if (!value || String(value).trim() === "") {
                if ($field.attr("id") === "item") {
                    $("#item_search_input").addClass("is-invalid");
                } else {
                    $field.addClass("is-invalid");
                }
                isValid = false;
            } else {
                if ($field.attr("id") === "item") {
                    $("#item_search_input").removeClass("is-invalid");
                } else {
                    $field.removeClass("is-invalid");
                }
            }
        });

        const order_id = $("#order_id").val();
        const supplierSelectedOption = $("#supplier").find("option:selected");
        const supplier_id = supplierSelectedOption.val();
        const supplierText = supplierSelectedOption.text().trim();

        const categorySelectedOption = $("#category").find("option:selected");
        const categoryText = categorySelectedOption.text().trim();
        const category_id = categorySelectedOption.val();

        const item_id = itemHiddenInput.value;
        const itemText = itemSearchInput.value.trim();
        const unit = $("#unit").val();
        const unitPrice = parseFloat($("#unit_price").val());

        const qnty = parseInt($("#qnty").val());

        if (isValid) {
            if (!itemText || qnty < 1 || unitPrice <= 0 || isNaN(unitPrice)) {
                Toast.fire({
                    title: "Please select an item and enter valid quantity/unit price.",
                    icon: "warning",
                });
                return;
            }

            let existingRow = null;

            orderTable.rows().every(function () {
                const rowData = this.data();
                if (rowData.item === itemText) {
                    existingRow = this;
                    return false;
                }
            });

            if (existingRow) {
                Swal.fire({
                    title: "Duplicate Entry?",
                    text: "This item is already added, do you want to update the quantity instead?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Confirm",
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }
                    $("#LoadingScreen").fadeIn(200);
                    const currentRowData = existingRow.data();

                    const newQnty = currentRowData.qnty + qnty;
                    const newTotal = newQnty * unitPrice;

                    currentRowData.qnty = newQnty;
                    currentRowData.total = newTotal.toFixed(2);

                    existingRow.data(currentRowData).draw();
                    $("#LoadingScreen").fadeOut(200);
                });
            } else {
                const total = qnty * unitPrice;

                const newRowData = {
                    order_id: order_id,
                    supplier: supplierText,
                    category: categoryText,
                    item: itemText,
                    item_id: item_id,
                    category_id: category_id,
                    supplier_id: supplier_id,
                    unit: unit,
                    qnty: qnty,
                    unit_price: unitPrice.toFixed(2),
                    total: total.toFixed(2),
                    action: `<button type="button" class="btn btn-sm btn-danger delete-row" data-item="${itemText}">Delete</button>`,
                };

                orderTable.row.add(newRowData).draw();
            }

            // Clear input fields after successful addition
            $("#category").val("");
            itemSearchInput.value = "";
            itemHiddenInput.value = "";
            availableItems = [];
            itemResultsContainer.classList.add("d-none");
            $("#unit").val("");
            $("#unit_price").val("");
            $("#qnty").val("");
        }
    });

    $("#orderRequest tbody").on("click", ".delete-row", function () {
        var row = orderTable.row($(this).parents("tr"));
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to Delete this Item.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Delete",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            $("#LoadingScreen").fadeIn(200);
            row.remove();
            orderTable.draw();
            // Clear input fields after deletion
            $("#category").val("");
            itemSearchInput.value = "";
            itemHiddenInput.value = "";
            availableItems = [];
            itemResultsContainer.classList.add("d-none");
            $("#unit").val("");
            $("#unit_price").val("");
            $("#qnty").val("");
            $("#LoadingScreen").fadeOut(200);
        });
    });

    $("#submit-PO").click(function (e) {
        e.preventDefault();

        var allTableData = orderTable.rows().data().toArray();
        var cleanedData = allTableData.map(function (item) {
            return {
                item_id: parseInt(item.item_id),
                supplier_id: parseInt(item.supplier_id),
                category_id: parseInt(item.category_id),
                qnty: item.qnty,
                unit_price: parseFloat(item.unit_price),
                total: parseFloat(item.total),
            };
        });
        if (cleanedData.length === 0) {
            Toast.fire({
                title: "The order is empty. Please add items before submitting.",
                icon: "warning",
                timer: 2000,
            });
            e.preventDefault();
            return;
        }
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to submit this Request.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Submit",
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            const jsonPayload = JSON.stringify(cleanedData);
            $("#order_items_payload").val(jsonPayload);
            const form = document.getElementById("submitPORequest");
            let formData = new FormData(form);
            $("#LoadingScreen").fadeIn(200);
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: `/procurement/create-purchase-request/submit-request`,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $("#order_id").val("");
                    $("#supplier").val("");
                    $("#category").val("");
                    // Clear item fields after submission
                    itemSearchInput.value = "";
                    itemHiddenInput.value = "";
                    availableItems = [];
                    itemResultsContainer.classList.add("d-none");

                    $("#unit").val("");
                    $("#unit_price").val("");
                    $("#qnty").val("");
                    $("#LoadingScreen").fadeOut(200);
                    orderTable.clear().draw();
                    reloadTable("purchaseRequestTable");
                    generateOrderId();
                    Toast.fire({
                        text: response.message,
                        icon: "success",
                    });
                },
                error: function (xhr) {
                    $("#LoadingScreen").fadeOut(200);
                    if (xhr.responseJSON?.errors) {
                        let errorMessages = Object.values(
                            xhr.responseJSON.errors
                        )
                            .flat()
                            .join("\n");
                        Toast.fire("Validation Error", errorMessages, "error");
                    } else {
                        Toast.fire(
                            "Error",
                            "An unexpected error occurred.",
                            "error"
                        );
                    }
                },
            });
        });
    });

    $("#submit-PR").click(function (e) {
        e.preventDefault();
        let supplier_id = $("#supplier").val();
        if ((!supplier_id || String(supplier_id).trim() === "") && selectedSupplierId) {
            $("#supplier").val(selectedSupplierId);
            supplier_id = selectedSupplierId;
        }

        let isValid = true;
        const $form = $("#submitPORequest");

        const $field = $form.find("select#supplier[required]");

        if ($field.length) {
            const value = $field.val();

            if (!value || String(value).trim() === "") {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        } else {
            Toast.fire({
                title: "The Supplier is empty. Please select supplier before submitting.",
                icon: "warning",
                timer: 2000,
            });
            e.preventDefault();
            return;
        }

        if (isValid) {
            var allTableData = orderTable.rows().data().toArray();
            var cleanedData = allTableData.map(function (item) {
                return {
                    item_id: parseInt(item.item_id),
                    supplier_id: parseInt(supplier_id),
                    category_id: parseInt(item.category_id),
                    qnty: item.qnty,
                    unit_price: parseFloat(item.unit_price),
                    total: parseFloat(item.total),
                };
            });
            const mismatchedRow = allTableData.find(function (item) {
                if (
                    item.supplier_id === null ||
                    item.supplier_id === undefined ||
                    item.supplier_id === ""
                ) {
                    return false;
                }
                return String(item.supplier_id) !== String(supplier_id);
            });
            if (mismatchedRow) {
                Toast.fire({
                    title: "Supplier Mismatch",
                    text: "All items must belong to the selected supplier.",
                    icon: "warning",
                });
                return;
            }
            if (cleanedData.length === 0) {
                Toast.fire({
                    title: "The order is empty. Please add items before submitting.",
                    icon: "warning",
                    timer: 2000,
                });
                e.preventDefault();
                return;
            }
            Swal.fire({
                title: "Are you sure?",
                text: "You are about to submit this Request.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Submit",
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                const jsonPayload = JSON.stringify(cleanedData);
                $("#order_items_payload").val(jsonPayload);
                const form = document.getElementById("submitPORequest");
                let formData = new FormData(form);
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    url: `/procurement/complete-purchase-request/submit-request`,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $("#order_id").val("");
                        $("#supplier").val("");
                        $("#category").val("");
                        // Clear item fields after submission
                        itemSearchInput.value = "";
                        itemHiddenInput.value = "";
                        availableItems = [];
                        itemResultsContainer.classList.add("d-none");

                        $("#unit").val("");
                        $("#unit_price").val("");
                        $("#qnty").val("");
                        $("#LoadingScreen").fadeOut(200);
                        orderTable.clear().draw();
                        reloadTable("purchaseRequestTable");
                        generateOrderId();
                        Toast.fire({
                            text: response.message,
                            icon: "success",
                        });
                        $("#createPrModal").modal("hide");
                    },
                    error: function (xhr) {
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
            });
        }
    });
});
