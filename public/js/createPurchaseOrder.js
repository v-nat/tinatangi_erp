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

    getSuppliers();
    getCategories();


    $('#addItem').on('click', function (e) {
        e.preventDefault();

        // --- 1. Get Values ---
        const itemElement = $('#item');
        const itemText = itemElement.find('option:selected').text().trim(); 
        const categoryElement = $('#category');
        const categoryText = categoryElement.find('option:selected').text().trim(); 
        const qnty = parseInt($('#qnty').val());
        const unitPrice = parseFloat($('#unit_price').val());

        // Basic Validation Check (Optional, but recommended)
        if (!itemText || qnty < 1 || unitPrice <= 0) {
            alert('Please select an item and enter valid quantity/unit price.');
            return;
        }

        // --- 2. Calculate Total ---
        const total = qnty * unitPrice;

        // --- 3. Create the Row Data Object ---
        // The keys MUST match the 'data' properties defined in your table initialization.
        const newRowData = {
            // 'null' for the auto-numbered column (#) is handled by DataTables itself
            category: categoryText,
            item: itemText,
            qnty: qnty,
            unit: unitPrice.toFixed(2), // Store as number/string, your renderer formats it with '₱'
            total: total.toFixed(2),     // Store as number/string, your renderer formats it with '₱'
            action: `<button type="button" class="btn btn-sm btn-danger delete-row" data-item="${itemText}">Delete</button>`
        };

        // --- 4. Add the Row and Redraw the Table ---
        // Use the API instance to add the new row object.
        orderTable.row.add(newRowData).draw();

        // Optional: Reset only the item-specific fields after adding
        $('#category').val('');
        $('#item_select').val('');
        $('#qnty').val('1');
    });

    $("#submit-PO").click(function (e) {
        e.preventDefault();

    });

    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
});
