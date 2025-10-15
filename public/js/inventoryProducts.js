$(function () {
    $("#products-table").DataTable({
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
            {
                data: "name",
                title: "Name",
                width: "500px",
            },
            {
                data: "base_price",
                title: "Base Price",
                className: "dt-left",
                render: $.fn.dataTable.render.number(",", ".", 2, "₱ "),
            },
            {
                data: "category_name",
                title: "Category",
                defaultContent: "N/A",
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
                    const recipeUrl = `/inventory/recipes/${row.id}/edit`;
                    const editUrl = `/inventory/products/${row.id}/edit`;

                    return `
                        <div class="btn-group" role="group">
                            <a href="${recipeUrl}" class="btn btn-sm btn-secondary" title="Manage Recipe">
                                <i class="fas fa-list-alt"></i> Recipe
                            </a>
                            <a href="${editUrl}" class="btn btn-sm btn-warning" title="Edit Product">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    `;
                },
            },
        ],
        language: {
            emptyTable: "No products found.",
            zeroRecords: "No matching products found.",
        },
    });

    $("#addProductBtn").on("click", function (e) {
        e.preventDefault();
        loadDropdownsIntoModal();
        $("#addProductModal").modal("show");
    });

    function loadDropdownsIntoModal() {
        // Now we only need to load categories
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
                // Once data is loaded, show the modal
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

        // 1. Validate Product Name (unchanged)
        const name = $("#name").val().trim();
        if (name === "") {
            isValid = false;
            $("#name").addClass("is-invalid");
            $("#name_error").text("Product name is required.");
        }

        // 2. Validate Base Price (unchanged)
        const price = $("#base_price").val();
        if (price === "" || parseFloat(price) < 0) {
            isValid = false;
            $("#base_price").addClass("is-invalid");
            $("#base_price_error").text(
                "Please enter a valid, non-negative price."
            );
        }

        // 3. Validate Category (unchanged)
        const category = $("#product_category_id").val();
        if (category === "") {
            isValid = false;
            $("#product_category_id").addClass("is-invalid");
            $("#product_category_id_error").text("Please select a category.");
        }

        // --- NEW: 4. Validate Image File ---
        const $imageInput = $("#image");
        if ($imageInput[0].files.length > 0) {
            // Only validate if a file is selected
            const file = $imageInput[0].files[0];
            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/gif",
                "image/jpg",
            ];
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            // Check file type
            if (!allowedTypes.includes(file.type)) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                $("#image_error").text(
                    "Invalid file type. Please use jpeg, png, or gif."
                );
            }

            // Check file size
            if (file.size > maxSize) {
                isValid = false;
                $imageInput.addClass("is-invalid");
                // If there was already a type error, append the size error. Otherwise, set it.
                const existingError = $("#image_error").text();
                const sizeError = "File is too large. Maximum size is 2MB.";
                $("#image_error").text(
                    existingError ? `${existingError} ${sizeError}` : sizeError
                );
            }
        }

        if (!isValid) {
            return;
        }

        $(".form-control, .form-select").removeClass("is-invalid");

        const formData = new FormData(this);

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
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                });
                $("#products-table").DataTable().ajax.reload();
            },
            error: function (xhr) {
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
