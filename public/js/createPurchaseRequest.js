$(document).ready(function () {
    generateOrderId();
    function generateOrderId() {
        fetch(`/procurement/generateOrderID/${'purchase_order'}`)
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


    // --- Searchable Item Dropdown Logic ---
    const itemSearchInput = document.getElementById("item_search_input");
    const itemHiddenInput = document.getElementById("item"); // The hidden field that stores the ID
    const itemResultsContainer = document.getElementById("item_results_container");
    const itemSearchList = document.getElementById("item_search_list");
    let availableItems = []; // Stores the items fetched from the API

    /**
     * Handles item selection from the dropdown list.
     * @param {Object} item - The selected item object {id, name, unit_id, unit_price}.
     */
    function handleItemSelection(item) {
        // Set the display text and the actual value
        itemSearchInput.value = item.name;
        itemHiddenInput.value = item.id;

        // Populate related fields
        $("#unit").val(item.unit_id);
        $("#unit_price").val(item.unit_price);

        // Hide the search results
        itemResultsContainer.classList.add("d-none");

        // Clear any previous validation errors
        $(itemSearchInput).removeClass("is-invalid");
    }

    /**
     * Renders the filtered list of items in the dropdown.
     * @param {Array<Object>} filteredItems - The items to display.
     */
    function renderItems(filteredItems) {
        itemSearchList.innerHTML = '';
        if (filteredItems.length === 0) {
            const noResult = document.createElement('a');
            noResult.className = 'list-group-item list-group-item-light small text-muted';
            noResult.textContent = 'No items found.';
            itemSearchList.appendChild(noResult);
        } else {
            filteredItems.forEach(item => {
                const itemLink = document.createElement('a');
                itemLink.className = 'list-group-item list-group-item-action py-2 small';
                itemLink.href = '#';
                itemLink.textContent = item.name;
                itemLink.dataset.itemId = item.id;

                // Add click listener to select the item
                itemLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    handleItemSelection(item);
                });
                itemSearchList.appendChild(itemLink);
            });
        }
        itemResultsContainer.classList.remove("d-none");
    }

    // Listener for input typing to filter results
    $(itemSearchInput).on('input', function() {
        const query = $(this).val().toLowerCase();

        // Clear the hidden ID and other dependent fields when user types
        itemHiddenInput.value = '';
        $("#unit").val("");
        $("#unit_price").val("");

        if (query.length === 0) {
            // If input is empty, show all items
            renderItems(availableItems);
            return;
        }

        // Filter items
        const filtered = availableItems.filter(item =>
            item.name.toLowerCase().includes(query)
        );
        renderItems(filtered);
    });

    // Listener for focus to show dropdown
    $(itemSearchInput).on('focus', function() {
        if (availableItems.length > 0) {
            const query = $(this).val().toLowerCase();
            const filtered = availableItems.filter(item => item.name.toLowerCase().includes(query));
            renderItems(filtered);
        }
    });

    // Hide results when clicking outside the component
    $(document).on('click', function(e) {
        const parentGroup = itemSearchInput.closest('.form-group');
        if (parentGroup && !parentGroup.contains(e.target)) {
            itemResultsContainer.classList.add("d-none");

            // If input value is not a valid item name, clear it
            const currentName = itemSearchInput.value.trim();
            const matchedItem = availableItems.find(item => item.name === currentName);

            if (currentName !== '' && !matchedItem) {
                itemSearchInput.value = '';
                itemHiddenInput.value = '';
            }
        }
    });

    // --- End of Searchable Item Dropdown Logic ---



    const categorySelect = document.getElementById("category");

    $("#category").change(function () {
        $("#unit").val("");
        $("#unit_price").val("");
        // Clear item fields when category changes
        itemSearchInput.value = '';
        itemHiddenInput.value = '';
        availableItems = []; // Clear current list of available items
        itemResultsContainer.classList.add("d-none");


        const category = categorySelect.value;
        if (category) {
            fetch(
                `/procurement/create-purchase-request/get-items?category=${encodeURIComponent(
                    category
                )}`
            )
                .then((res) => res.json())
                .then((data) => {
                    // Store fetched items globally
                    availableItems = data.map(p => ({
                        id: p.id,
                        name: p.name,
                        unit_id: p.unit_id,
                        unit_price: p.unit_price
                    }));

                    // Automatically show the full list when items are loaded
                    renderItems(availableItems);
                })
                .catch((error) => {
                    console.error("Error fetching items:", error);
                    Toast.fire("Error", "Could not load items for the selected category.", "error");
                    availableItems = [];
                });
        }
    });

    function getSuppliers() {
        const supplierSelect = document.getElementById("supplier");

        fetch(`/procurement/create-purchase-request/get-active-supplier`)
            .then((response) => response.json())
            .then((data) => {
                supplierSelect.innerHTML =
                    '<option value="" disabled selected>Choose...</option>';
                data.forEach((s) => {
                    const option = document.createElement("option");
                    option.value = s.id;
                    option.textContent = s.supplier_name;
                    supplierSelect.appendChild(option);
                });
            });
    }

    $("#supplier").change(function () {
        if (orderTable.rows().count() > 0) {
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
                    return;
                }
                $("#category").val("");
                // Clear item fields after confirmation
                itemSearchInput.value = '';
                itemHiddenInput.value = '';
                availableItems = [];
                itemResultsContainer.classList.add("d-none");

                $("#unit").val("");
                $("#unit_price").val("");
                $("#qnty").val("");
                orderTable.clear().draw();
            });
        }
    });

    $(document).ready( function () {
        fetch(`/procurement/create-purchase-request/get-categories`)
            .then((response) => response.json())
            .then((data) => {
                categorySelect.innerHTML =
                    '<option value="" disabled selected>Choose Category</option>';
                data.forEach((s) => {
                    const option = document.createElement("option");
                    option.value = s.id;
                    option.textContent = s.name;
                    categorySelect.appendChild(option);
                });
            });
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

        // The validation logic must now check the hidden 'item' field for its value,
        // but mark the VISIBLE 'item_search_input' with the invalid class.
        $form.find("input[required], select[required]").each(function () {
            const $field = $(this);
            const value = $field.val();

            if (!value || String(value).trim() === "") {
                if ($field.attr('id') === 'item') {
                    // Apply invalid class to the visible search input
                    $("#item_search_input").addClass("is-invalid");
                } else {
                    $field.addClass("is-invalid");
                }
                isValid = false;
            } else {
                if ($field.attr('id') === 'item') {
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

        // Get data directly from the hidden input and other fields
        const item_id = itemHiddenInput.value;
        const itemText = itemSearchInput.value.trim();
        const unit = $("#unit").val(); // unit is now taken from the input field
        const unitPrice = parseFloat($("#unit_price").val()); // price is taken from the input field

        const qnty = parseInt($("#qnty").val());

        if (isValid) {
            if (!itemText || qnty < 1 || unitPrice <= 0 || isNaN(unitPrice)) {
                Toast.fire({
                    title: "Please select an item and enter valid quantity/unit price.",
                    icon: "warning"
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
            itemSearchInput.value = '';
            itemHiddenInput.value = '';
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
            itemSearchInput.value = '';
            itemHiddenInput.value = '';
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
                    itemSearchInput.value = '';
                    itemHiddenInput.value = '';
                    availableItems = [];
                    itemResultsContainer.classList.add("d-none");

                    $("#unit").val("");
                    $("#unit_price").val("");
                    $("#qnty").val("");
                    $("#LoadingScreen").fadeOut(200);
                    generateOrderId();
                    orderTable.clear().draw();
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
});
