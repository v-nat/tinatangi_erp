$(function () {
    const productsTable = $("#products-table").DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "/inventory/products/get",
            dataSrc: "data",
        },
        columns: [
            {
                title: "#",
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                },
                className: "text-center",
                width: "45px",
            },
            { data: "name", title: "Name", width: "350px" },
            { data: "desc", title: "Description", width: "280px" },
            {
                data: "base_price",
                title: "Base Price",
                className: "dt-left",
                render: $.fn.dataTable.render.number(",", ".", 2, "₱ "),
            },
            { data: "category_name", title: "Category", defaultContent: "N/A" },
            {
                data: null,
                title: "Servings Available",
                className: "text-center",
                defaultContent: '<i class="fas fa-spinner fa-spin"></i>',
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
                    const editUrl = `/inventory/products/${row.id}/edit`;
                    return `<div class="action-btns" role="group">
                                <button class="btn btn-sm btn-info manage-recipe-btn" title="Manage Recipe" data-product-id="${row.id}">
                                    <i class="fas fa-list-alt"></i>
                                </button>
                                <a href="${editUrl}" class="btn btn-sm btn-warning" title="Edit Product"><i class="fas fa-edit"></i></a>
                            </div>`;
                },
            },
            { data: "id", visible: false },
        ],
        createdRow: function(row, data, dataIndex) {
            const servingsCell = $('td', row).eq(-3);

            $.get(`/inventory/products/${data.id}/servings`, function(response) {
                servingsCell.text(response.servings);
            }).fail(function() {
                servingsCell.text('Error');
            });
        },
        language: {
            emptyTable: "No products found.",
            zeroRecords: "No matching products found.",
        },
    });

    $("#addProductBtn").on("click", function (e) {
        e.preventDefault();
        $("#addProductForm")[0].reset();
        $(".form-control, .form-select").removeClass("is-invalid");
        loadDropdownsIntoModal();
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

    $("#addProductForm").on("submit", function (e) {
        e.preventDefault();
        let isValid = true;
        $(".form-control, .form-select").removeClass("is-invalid");

        const name = $("#name").val().trim();
        if (name === "") {
            isValid = false;
            $("#name").addClass("is-invalid");
            $("#name_error").text("Product name is required.");
        }

        const price = $("#base_price").val();
        if (price === "" || parseFloat(price) < 0) {
            isValid = false;
            $("#base_price").addClass("is-invalid");
            $("#base_price_error").text(
                "Please enter a valid, non-negative price."
            );
        }

        const category = $("#product_category_id").val();
        if (category === "") {
            isValid = false;
            $("#product_category_id").addClass("is-invalid");
            $("#product_category_id_error").text("Please select a category.");
        }

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
                    "Invalid file type. Please use jpeg, png, or gif."
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

        const formData = new FormData(this);
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
                    for (const key in errors) {
                        const field = $(`#${key}`);
                        field.addClass("is-invalid");
                        $(`#${key}_error`).text(errors[key][0]);
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
});
