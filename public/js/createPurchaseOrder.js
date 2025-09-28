$(document).ready(function () {
    $("#orderRequest").DataTable({
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

    $("#submit-PO").click(function (e) {
        e.preventDefault();

        console.log('add btn clicked');
    });

    function reloadTable(tableId) {
        $("#" + tableId)
            .DataTable()
            .ajax.reload(null, false);
    }
});
