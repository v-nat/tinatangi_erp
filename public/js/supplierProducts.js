import { reloadTable } from "./utils/reloadTable.js";

document.addEventListener("DOMContentLoaded", () => {
    if (!document.querySelector("#supplier-products-table")) {
        return;
    }

    const $tableRefresh = $("#btn-refresh-supplier-products");
    const $addProductBtn = $("#btn-add-supplier-product");
    const $productModal = $("#supplierProductModal");
    const $productForm = $("#supplierProductForm");
    const $modalTitle = $("#supplierProductModalLabel");
    const $submitBtn = $("#productSubmitBtn");

    let productDataTable = null;
    let isEditMode = false;
    let editingProductId = null;
    let cachedOptions = null;

    function formatCurrency(value) {
        const number = Number(value);
        if (Number.isNaN(number)) {
            return "₱0.00";
        }
        return new Intl.NumberFormat("en-PH", {
            style: "currency",
            currency: "PHP",
            minimumFractionDigits: 2,
        }).format(number);
    }

    function resetProductForm() {
        $productForm[0].reset();
        $productForm.find(".is-invalid").removeClass("is-invalid");
        $productForm.find(".invalid-feedback").hide();
        editingProductId = null;
        isEditMode = false;
        $modalTitle.text("Add Product");
        $submitBtn.text("Save Product");
    }

    function populateOptions(options) {
        cachedOptions = options;
        const categories = Array.isArray(options?.categories)
            ? options.categories
            : [];
        const units = Array.isArray(options?.units) ? options.units : [];

        const fillSelect = (selector, data, placeholder) => {
            const list = Array.isArray(data) ? data : [];
            const $select = $(selector);
            $select.empty();
            if (placeholder) {
                $select.append(
                    `<option value="" disabled selected>${placeholder}</option>`
                );
            }
            list.forEach((item) => {
                const label = item.abbreviation
                    ? `${item.name} (${item.abbreviation})`
                    : item.name;
                $select.append(`<option value="${item.id}">${label}</option>`);
            });
        };

        fillSelect("#product_category", categories, "Select Category");
        fillSelect("#product_unit", units, "Select Unit");
    }

    function fetchOptions() {
        return $.getJSON("/supplier/products/options")
            .done((options) => {
                populateOptions(options);
            })
            .fail(() => {
                Toast.fire({
                    title: "Error",
                    text: "Unable to load product options.",
                    icon: "error",
                });
            });
    }

    function initDataTable() {
        productDataTable = $("#supplier-products-table").DataTable({
            responsive: true,
            processing: true,
            ajax: {
                url: "/supplier/products/list",
                dataSrc: "data",
            },
            columns: [
                { data: "name" },
                { data: "category_name", defaultContent: "—" },
                { data: "unit_name", defaultContent: "—" },
                {
                    data: "unit_price_formatted",
                    render: function (data, type, row) {
                        if (type === "display") {
                            return formatCurrency(row.unit_price);
                        }
                        return row.unit_price;
                    },
                },
                {
                    data: "status_html",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "updated_at",
                    defaultContent: "—",
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-info btn-edit-product" data-id="${row.id}" title="Edit Product">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                        `;
                    },
                },
            ],
        });
    }

    function openCreateModal() {
        resetProductForm();
        if (!cachedOptions) {
            fetchOptions().then(() => {
                $productModal.modal("show");
            });
        } else {
            populateOptions(cachedOptions);
            $productModal.modal("show");
        }
    }

    function openEditModal(productId) {
        $.getJSON(`/supplier/products/${productId}`)
            .done((response) => {
                const item = response.data;
                if (!cachedOptions) {
                    fetchOptions().then(() => fillEditForm(item));
                } else {
                    populateOptions(cachedOptions);
                    fillEditForm(item);
                }
            })
            .fail(() => {
                Toast.fire({
                    title: "Error",
                    text: "Unable to load product details.",
                    icon: "error",
                });
            });
    }

    function fillEditForm(item) {
        resetProductForm();
        isEditMode = true;
        editingProductId = item.id;
        $modalTitle.text("Edit Product");
        $submitBtn.text("Update Product");

        $("#product_id").val(item.id);
        $("#product_name").val(item.name ?? "");
        $("#product_category").val(item.category_id).trigger("change");
        $("#product_unit").val(item.unit_id).trigger("change");
        $("#product_price").val(item.unit_price);
        $("#product_status").val(item.status ?? 1);

        $productModal.modal("show");
    }

    function submitProductForm() {
        const formData = new FormData($productForm[0]);
        formData.delete("inventory_location_id");
        let url = "/supplier/products";
        let method = "POST";

        if (isEditMode && editingProductId) {
            url = `/supplier/products/${editingProductId}`;
            formData.append("_method", "PUT");
        }

        $submitBtn.prop("disabled", true).text("Saving...");

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $productModal.modal("hide");
                reloadTable("supplier-products-table");
                resetProductForm();
                Toast.fire({
                    title: "Success",
                    text: response.message || "Product saved successfully!",
                    icon: "success",
                });
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach((field) => {
                        const $input = $productForm.find(`[name="${field}"]`);
                        if ($input.length) {
                            $input.addClass("is-invalid");
                            const $feedback =
                                $input.siblings(".invalid-feedback");
                            if ($feedback.length) {
                                $feedback.text(errors[field][0]).show();
                            }
                        }
                    });
                }
                Toast.fire({
                    title: "Error",
                    text: xhr.responseJSON?.error || "Unable to save product.",
                    icon: "error",
                });
            },
            complete: function () {
                $submitBtn
                    .prop("disabled", false)
                    .text(isEditMode ? "Update Product" : "Save Product");
            },
        });
    }

    if ($tableRefresh.length) {
        $tableRefresh.on("click", function () {
            reloadTable("supplier-products-table");
        });
    }

    if ($addProductBtn.length) {
        $addProductBtn.on("click", function () {
            openCreateModal();
        });
    }

    $(document).on("click", ".btn-edit-product", function () {
        const productId = $(this).data("id");
        openEditModal(productId);
    });

    $productModal.on("hidden.bs.modal", function () {
        resetProductForm();
    });

    $productForm.on("submit", function (event) {
        event.preventDefault();
        submitProductForm();
    });

    fetchOptions().then(() => {
        initDataTable();
    });
});
