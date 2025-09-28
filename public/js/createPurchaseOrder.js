$(document).ready(function () {
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
            },
            {
                data: "qnty",
            },
            {
                data: "unit",
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },
            {
                data: "total",
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },

            {
                data: "action",
            },
        ],
    });

    const categorySelect = document.getElementById("category");
    const itemSelect = document.getElementById("item");

    categorySelect.addEventListener("change", getItems);

    function getSuppliers() {
        const supplierSelect = document.getElementById("supplier");

        fetch(`/procurement/create-purchase-order/get-active-supplier`)
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
    function getCategories() {
        fetch(`/procurement/create-purchase-order/get-categories`)
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
    }

    function getItems() {
        const category = categorySelect.value;
        if (category) {
            fetch(
                `/procurement/create-purchase-order/get-items?category=${encodeURIComponent(
                    category
                )}`
            )
                .then((res) => res.json())
                .then((data) => {
                    const itemSelect = document.getElementById("item");
                    itemSelect.innerHTML =
                        '<option value="" disabled selected>Choose Item</option>';

                    data.forEach((p) => {
                        const option = document.createElement("option");
                        option.value = p.id;
                        option.textContent = p.name;
                        itemSelect.appendChild(option);
                    });
                });
        }
    }

    $(document).ready(function () {
        const categorySelect = document.getElementById("category");
        categorySelect.addEventListener("change", clearInvalids);
    });

    function clearInvalids() {
        const $form = $("#submitPORequest");
        $form.find("input, select, option").each(function () {
            const $field = $(this);
            $field.removeClass("is-invalid");
        });
    }

    getSuppliers();
    getCategories();

    $('#addItem').on('click', function (e) {
        e.preventDefault();

        let isValid = true;
        const $form = $("#submitPORequest");

        $form.find("input, select, option").each(function () {
            const $field = $(this);
            const value = $field.val();
            if ($field.prop("required") && (!value || !value.trim())) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        const itemElement = $('#item');
        const itemText = itemElement.find('option:selected').text().trim();
        const categoryElement = $('#category');
        const categoryText = categoryElement.find('option:selected').text().trim();
        const qnty = parseInt($('#qnty').val());
        const unitPrice = parseFloat($('#unit_price').val());

        if (isValid) {
            // Basic Validation Check (Optional, but recommended)
            if (!itemText || qnty < 1 || unitPrice <= 0) {
                alert('Please select an item and enter valid quantity/unit price.');
                return;
            }

            // --- NEW LOGIC: Check for Existing Item ---
            let existingRow = null;

            // Iterate through all data in the table
            orderTable.rows().every(function () {
                const rowData = this.data();

                // Check if the item in the current row matches the new item text
                if (rowData.item === itemText) {
                    existingRow = this; // Store the DataTables row object
                    return false; // Exit the loop (like a 'break')
                }
            });

            // --- 3. Handle Item Found or Not Found ---
            if (existingRow) {
                // ITEM FOUND: Update the quantity and total
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

                    // **Calculate the NEW Quantity and Total**
                    const newQnty = currentRowData.qnty + qnty;
                    const newTotal = newQnty * unitPrice;

                    // Update the row data object
                    currentRowData.qnty = newQnty;
                    currentRowData.total = newTotal.toFixed(2);

                    // Use row().data(newData) to update the row's data
                    existingRow.data(currentRowData).draw();
                    $("#LoadingScreen").fadeOut(200);
                });

            } else {
                // ITEM NOT FOUND: Add a new row

                // --- Calculate Total (from original logic) ---
                const total = qnty * unitPrice;

                // --- Create the Row Data Object ---
                const newRowData = {
                    category: categoryText,
                    item: itemText,
                    qnty: qnty,
                    unit: unitPrice.toFixed(2),
                    total: total.toFixed(2),
                    action: `<button type="button" class="btn btn-sm btn-danger delete-row" data-item="${itemText}">Delete</button>`
                };

                // --- Add the New Row and Redraw the Table ---
                orderTable.row.add(newRowData).draw();
            }
            // Optional: Reset only the item-specific fields after adding
            $('#category').val('');
            $('#item_select').val('');
            $('#qnty').val('1');
        }

    });

    $('#orderRequest tbody').on('click', '.delete-row', function () {
        var row = orderTable.row($(this).parents('tr'));
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
            $('#category').val('');
            $('#item_select').val('');
            $('#qnty').val('1');
            $("#LoadingScreen").fadeOut(200);
        });
    });

    $("#submit-PO").click(function (e) {
        e.preventDefault();

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
            console.log('SUBMIT CLICKED');
            // var allTableData = orderTable.rows().data().toArray();

            // var cleanedData = allTableData.map(function (item) {
            //     return {
            //         item: item.item,
            //         qnty: item.qnty,
            //         unit: parseFloat(item.unit), 
            //         total: parseFloat(item.total), 
            //     };
            // });
            // if (cleanedData.length === 0) {
            //     alert("The order is empty. Please add items before submitting.");
            //     e.preventDefault();
            //     return;
            // }
            // const jsonPayload = JSON.stringify(cleanedData);
            // $('#order_items_payload').val(jsonPayload);
        });

    });

    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
});
