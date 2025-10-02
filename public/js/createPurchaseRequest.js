$(document).ready(function () {
    generateOrderId();
    function generateOrderId() {
        fetch(`/procurement/generateOrderID`)
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
                data: "item", className: "dt-left" 
            },
            {
                data: "unit", className: "dt-left" 
            },
            {
                data: "qnty", className: "dt-left" 
            },
            {
                data: "unit_price", className: "dt-left" ,
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },
            {
                data: "total", className: "dt-left" ,
                render: function (data) {
                    return "₱" + parseFloat(data).toFixed(2);
                },
            },

            {
                data: "action",
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

    const categorySelect = document.getElementById("category");
    const itemSelect = document.getElementById("item");

    categorySelect.addEventListener("change", getItems);
    itemSelect.addEventListener("change", itemSelected);

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
    function getCategories() {
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
    }

    function getItems() {
        $("#unit").val("");
        $("#unit_price").val("");
        const category = categorySelect.value;
        if (category) {
            fetch(
                `/procurement/create-purchase-request/get-items?category=${encodeURIComponent(
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
                        option.dataset.unit = p.unit;
                        option.dataset.unit_price = p.unit_price;
                        option.textContent = p.name;
                        itemSelect.appendChild(option);
                    });
                });
        }
    }

    $(document).ready(function () {
        const categorySelect = document.getElementById("category");
        const nameInput = document.getElementById("order_id");
        categorySelect.addEventListener("change", clearInvalids);
        nameInput.addEventListener("change", clearInvalids);
    });

    function clearInvalids() {
        const $form = $("#submitPORequest");
        $form.find("input, select, option").each(function () {
            const $field = $(this);
            $field.removeClass("is-invalid");
        });
    }
    function itemSelected() {
        const selectedOption = $("#item").find("option:selected");
        const unit = selectedOption.data("unit");
        const unitPrice = selectedOption.data("unit_price");
        $("#unit").val(unit);
        $("#unit_price").val(unitPrice);
    }

    getSuppliers();
    getCategories();

    $("#addItem").on("click", function (e) {
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

        const order_id = $("#order_id").val();
        const supplierSelectedOption = $("#supplier").find("option:selected");
        const supplier_id = supplierSelectedOption.val();
        const supplierText = supplierSelectedOption.text().trim();

        const categorySelectedOption = $("#category").find("option:selected");
        const categoryText = categorySelectedOption.text().trim();
        const category_id = categorySelectedOption.val();

        const itemSelectedOption = $("#item").find("option:selected");
        const itemText = itemSelectedOption.text().trim();
        const unit = itemSelectedOption.data("unit");
        const item_id = itemSelectedOption.val();
        const unitPrice = parseFloat(itemSelectedOption.data("unit_price"));

        const qnty = parseInt($("#qnty").val());

        if (isValid) {
            if (!itemText || qnty < 1 || unitPrice <= 0) {
                Toast.fire(
                    "Please select an item and enter valid quantity/unit price."
                );
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

            $("#category").val("");
            $("#item").val("");
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
            $("#category").val("");
            $("#item").val("");
            $("#unit").val("");
            $("#unit_price").val("");
            $("#qnty").val("");
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
            // console.log(cleanedData);
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
                    $("#item").val("");
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
