$(function () {
    var productsTable;

    productsTable = $("#products-table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/inventory/products/get",
            dataSrc: "data",
        },
        order: [[1, "asc"]],
        columns: [
            {
                title: '<input type="checkbox" class="form-check-input" id="select-all-checkbox">',
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center dt-checkbox",
                render: function (data, type, row) {
                    return `<input type="checkbox" class="form-check-input" value="${row.id}">`;
                },
            },
            { data: "name", title: "Name" },
            { data: "desc", title: "Description" },
            {
                data: "base_price",
                title: "Base Price",
                className: "dt-left",
                render: $.fn.dataTable.render.number(",", ".", 2, "₱ "),
            },
            { data: "category_name", title: "Category", defaultContent: "N/A" },
            {
                data: "available_servings",
                title: "Servings",
                className: "text-center",
                defaultContent: "N/A",
                render: function (data, type, row) {
                    if (data === null || data === undefined) {
                        return "N/A";
                    }
                    const parsed = parseInt(data, 10);
                    return Number.isNaN(parsed) ? "N/A" : parsed;
                },
            },
            {
                data: "status",
                title: "Status",
                className: "text-center",
                defaultContent: "N/A",
            },
            {
                data: null,
                title: "Actions",
                className: "text-center",
                orderable: false,
                render: function (data, type, row) {
                    return (
                        '<div class="btn-group btn-group-sm" role="group">' +
                        `<button class="btn btn-primary view-product-btn" title="View Product" data-product-id="${row.id}">` +
                        '<i class="fas fa-eye"></i>' +
                        "</button>" +
                        `<button class="btn btn-info manage-recipe-btn" title="Manage Recipe" data-product-id="${row.id}">` +
                        '<i class="fas fa-list-alt"></i>' +
                        "</button>" +
                        `<button class="btn btn-warning edit-product-btn" title="Edit Product" data-product-id="${row.id}">` +
                        '<i class="fas fa-edit"></i>' +
                        "</button>" +
                        `<button class="btn btn-danger delete-product-btn" title="Delete Product" data-product-id="${row.id}">` +
                        '<i class="fas fa-trash"></i>' +
                        "</button>" +
                        "</div>"
                    );
                },
            },
            { data: "id", visible: false },
        ],
        drawCallback: function (settings) {
            $("#select-all-checkbox").prop("checked", false);
            toggleBatchDeleteButton();
            var tooltipTriggerList = [].slice.call(
                document.querySelectorAll("#products-table [title]")
            );
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                tooltipTriggerEl.setAttribute("data-bs-toggle", "tooltip");
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },
        language: {
            emptyTable: "No products found.",
            zeroRecords: "No matching products found.",
        },
        initComplete: function () {
            const column = this.api().column(4);
            const select = $("#category_filter");

            column
                .data()
                .unique()
                .sort()
                .each(function (d, j) {
                    if (d) {
                        select.append(
                            $("<option></option>").attr("value", d).text(d)
                        );
                    }
                });
        },
    });

    $("#btn-refresh-products").on("click", function () {
        if (productsTable) {
            productsTable.ajax.reload(null, false);
        }
    });

    function toggleBatchDeleteButton() {
        if (productsTable) {
            const selectedCount = productsTable
                .rows()
                .nodes()
                .to$()
                .find(".row-checkbox:checked").length;
            if (selectedCount > 0) {
                $("#batchDeleteBtn")
                    .show()
                    .text(`Delete (${selectedCount}) Selected`);
            } else {
                $("#batchDeleteBtn").hide();
            }
        } else {
            $("#batchDeleteBtn").hide();
        }
    }

    $("#products-table").on("click", "#select-all-checkbox", function () {
        const isChecked = $(this).is(":checked");
        productsTable
            .rows()
            .nodes()
            .to$()
            .find(".row-checkbox")
            .prop("checked", isChecked);
        toggleBatchDeleteButton();
    });

    $("#products-table tbody").on("click", ".row-checkbox", function () {
        if (!$(this).is(":checked")) {
            $("#select-all-checkbox").prop("checked", false);
        } else {
            if (
                productsTable
                    .rows()
                    .nodes()
                    .to$()
                    .find(".row-checkbox:not(:checked)").length === 0
            ) {
                $("#select-all-checkbox").prop("checked", true);
            }
        }
        toggleBatchDeleteButton();
    });

    $("#category_filter").on("change", function () {
        const selectedCategory = $(this).val();

        productsTable
            .column(4)
            .search(
                selectedCategory ? "^" + selectedCategory + "$" : "",
                true,
                false
            )
            .draw();
    });

    $("#batchDeleteBtn").on("click", function () {
        const selectedIds = [];
        productsTable
            .rows()
            .nodes()
            .to$()
            .find(".row-checkbox:checked")
            .each(function () {
                selectedIds.push($(this).val());
            });

        if (selectedIds.length === 0) {
            Toast.fire({ icon: "error", title: "No items selected." });
            return;
        }

        Swal.fire({
            title: `Are you sure?`,
            text: `You are about to delete ${selectedIds.length} products. You won't be able to revert this!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete them!",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);
                $.ajax({
                    url: "/inventory/products/batch-delete",
                    type: "POST",
                    data: {
                        ids: selectedIds,
                    },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (response) {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: response.message,
                            timer: 1500,
                        });
                        productsTable.ajax.reload();
                        toggleBatchDeleteButton();
                    },
                    error: function (xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        Swal.fire(
                            "Error",
                            "Could not delete the selected products.",
                            "error"
                        );
                    },
                });
                _;
            }
        });
    });

    $("#addProductBtn").on("click", function (e) {
        e.preventDefault();
        $("#addProductForm")[0].reset();
        $(".form-control, .form-select").removeClass("is-invalid");
        loadDropdownsIntoModal();
    });

    $("#products-table tbody").on("click", ".view-product-btn", function () {
        const productId = $(this).data("product-id");

        $("#productViewName").text("Loading...");
        $("#productViewCategory").text("");
        $("#productViewDescription").text("Please wait...");
        $("#productViewPrice").text("...");
        $("#productViewStatus").text("").removeClass("bg-success bg-secondary");
        $("#productViewImage").attr(
            "src",
            "https://via.placeholder.com/350x350.png?text=Loading..."
        );

        $("#viewProductModal").modal("show");

        $.ajax({
            url: `/inventory/products/${productId}`,
            type: "GET",
            success: function (data) {
                const product = data.product;

                $("#productViewName").text(product.name);
                $("#productViewCategory").text(product.category_name);
                $("#productViewDescription").text(
                    product.description || "No description provided."
                );

                const price = parseFloat(product.base_price);
                $("#productViewPrice").text(`₱ ${price.toFixed(2)}`);

                $("#productViewStatus").text(product.status_text);
                if (product.status == 1) {
                    $("#productViewStatus").addClass("bg-success");
                    $("#productViewStatus").text("Available");
                } else {
                    $("#productViewStatus").addClass("bg-danger");
                    $("#productViewStatus").text("Unavailable");
                }

                if (product.image) {
                    $("#productViewImage").attr(
                        "src",
                        `/storage/app/public/${product.image}`
                    );
                } else {
                    $("#productViewImage").attr(
                        "src",
                        "https://via.placeholder.com/350x350.png?text=No+Image"
                    );
                }
            },
            error: function () {
                $("#viewProductModal").modal("hide");
                Swal.fire(
                    "Error",
                    "Could not retrieve product details.",
                    "error"
                );
            },
        });
    });

    function loadDropdownsIntoModal() {
        $.ajax({
            url: "/inventory/products/get-categories",
            type: "GET",
            success: function (categories) {
                const $categorySelect = $("#product_category_id");
                $categorySelect
                    .empty()
                    .append('<option value="">Select a category...</option>');
                categories.forEach(function (category) {
                    $categorySelect.append(
                        `<option value="${category.id}">${category.name}</option>`
                    );
                });
                $("#addProductModal").modal("show");
            },
            error: function () {
                Swal.fire(
                    "Error",
                    "Could not load categories for the form.",
                    "error"
                );
            },
        });
    }

    function loadCategoriesIntoEditModal(selectedCategoryId) {
        $.ajax({
            url: "/inventory/products/get-categories",
            type: "GET",
            success: function (categories) {
                const $select = $("#edit_product_category_id");
                $select
                    .empty()
                    .append('<option value="">Select a category...</option>');

                categories.forEach(function (category) {
                    const isSelected =
                        category.id == selectedCategoryId ? "selected" : "";
                    $select.append(
                        `<option value="${category.id}" ${isSelected}>${category.name}</option>`
                    );
                });

                $("#editProductModal").modal("show");
                content;
            },
            error: function () {
                Swal.fire(
                    "Error",
                    "Could not load categories for the edit form.",
                    "error"
                );
            },
        });
    }

    $("#products-table tbody").on("click", ".edit-product-btn", function () {
        const productId = $(this).data("product-id");

        $("#editProductForm")[0].reset();
        $(".form-control, .form-select").removeClass("is-invalid");
        $("#current_image_preview").attr(
            "src",
            "https://via.placeholder.com/150"
        );

        $.ajax({
            url: `/inventory/products/${productId}`,
            type: "GET",
            success: function (data) {
                const product = data.product;

                $("#edit_product_id").val(product.id);
                $("#edit_name").val(product.name);
                $("#edit_base_price").val(product.base_price);
                $("#edit_description").val(product.description || "");

                if (product.image) {
                    $("#current_image_preview").attr(
                        "src",
                        `/storage/app/public/${product.image}`
                    );
                } else {
                    $("#current_image_preview").attr(
                        "src",
                        "https://via.placeholder.com/150?text=No+Image"
                    );
                }

                loadCategoriesIntoEditModal(product.product_category_id);
            },
            error: function () {
                Swal.fire(
                    "Error",
                    "Could not retrieve product details for editing.",
                    "error"
                );
            },
        });
    });

    $("#submitEditProduct").on("click", function (e) {
        e.preventDefault();
        let isValid = true;
        const $form = $("#editProductForm");

        $form.find("input[required], select[required]").each(function () {
            const $field = $(this);
            if (!$field.val() || ($field.is("input") && !$field.val().trim())) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        const $imageInput = $("#edit_image");
        if ($imageInput[0].files.length > 0) {
            const file = $imageInput[0].files[0];
            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/gif",
                "image/jpg",
            ];
            const maxSize = 10 * 1024 * 1024;

            $("#edit_image_error").text("");

            if (!allowedTypes.includes(file.type)) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                $("#edit_image_error").text(
                    "Invalid file type. Please use jpg, jpeg, or png."
                );
            }
            if (file.size > maxSize) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                const existingError = $("#edit_image_error").text();
                const sizeError = "File is too large. Maximum size is 10MB.";
                $("#edit_image_error").text(
                    existingError ? `${existingError} ${sizeError}` : sizeError
                );
            }
        }

        if (!isValid) return;

        const formData = new FormData($form[0]);
        const productId = $("#edit_product_id").val();

        $("#LoadingScreen").fadeIn(200);

        $.ajax({
            url: `/inventory/products/update/${productId}`,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#editProductModal").modal("hide");
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                });
                productsTable.ajax.reload();
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    for (const key in errors) {
                        const field = $(`#edit_${key}`);
                        field.addClass("is-invalid");
                        $(`#edit_${key}_error`).text(errors[key][0]);
                    }
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong! Please try again.",
                    });
                }
            },
        });
    });

    $("#submitProduct").on("click", function (e) {
        e.preventDefault();
        let isValid = true;

        const $form = $("#addProductForm");

        $form.find("input, number, select, option").each(function () {
            const $field = $(this);
            const value = $field.val();
            if ($field.prop("required") && (!value || !value.trim())) {
                $field.addClass("is-invalid");
                isValid = false;
            } else {
                $field.removeClass("is-invalid");
            }
        });

        const $imageInput = $("#image");
        if ($imageInput[0].files.length > 0) {
            const file = $imageInput[0].files[0];
            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/gif",
                "image/jpg",
            ];
            const maxSize = 10 * 1024 * 1024;

            if (!allowedTypes.includes(file.type)) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                $("#image_error").text(
                    "Invalid file type. Please use jpg, jpeg, or png."
                );
            }

            if (file.size > maxSize) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                const existingError = $("#image_error").text();
                const sizeError = "File is too large. Maximum size is 10MB.";
                $("#image_error").text(
                    existingError ? `${existingError} ${sizeError}` : sizeError
                );
            }
        }

        if (!isValid) {
            return;
        }

        const formData = new FormData($("#addProductForm")[0]);
        $("#LoadingScreen").fadeIn(200);
        $.ajax({
            url: "/inventory/products/store",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                $("#addProductModal").modal("hide");
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                });
                productsTable.ajax.reload();
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorHtml =
                        '<ul class="mb-0 text-start list-unstyled">';

                    for (const key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            errorHtml += `<li>${errors[key][0]}</li>`;
                        }
                    }
                    errorHtml += "</ul>";

                    Toast.fire({
                        icon: "error",
                        title: "Validation Failed",
                        html: errorHtml,
                    });
                } else {
                    Toast.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong! Please try again.",
                    });
                }
            },
        });
    });

    $("#products-table tbody").on("click", ".delete-product-btn", function () {
        const productId = $(this).data("product-id");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#LoadingScreen").fadeIn(200);

                $.ajax({
                    url: `/inventory/products/${productId}`,
                    type: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (response) {
                        $("#LoadingScreen").fadeOut(200);
                        Toast.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: response.message,
                            timer: 1500,
                        });
                        productsTable.ajax.reload();
                    },
                    error: function (xhr) {
                        $("#LoadingScreen").fadeOut(200);
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Something went wrong! Please try again.",
                        });
                    },
                });
            }
        });
    });

    $(document).on("keydown", "#base_price, #edit_base_price", function (e) {
        const key = e.key;
        const value = $(this).val();
        const selection = this.selectionStart;

        if (
            [
                "Backspace",
                "Delete",
                "Tab",
                "Escape",
                "Enter",
                "ArrowLeft",
                "ArrowRight",
                "Home",
                "End",
            ].includes(key)
        ) {
            return;
        }
        if (key === "." && value.includes(".")) {
            e.preventDefault();
            return;
        }

        if (!/^[0-9.]$/.test(key)) {
            e.preventDefault();
            return;
        }

        if (value.includes(".") && selection > value.indexOf(".")) {
            const decimalPart = value.substring(value.indexOf(".") + 1);
            if (
                decimalPart.length >= 2 &&
                document.getSelection().toString().length === 0
            ) {
                // If we are not overwriting a selection, prevent typing
                e.preventDefault();
            }
        }
    });
});
