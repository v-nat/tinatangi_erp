$(function () {
    let allInventoryItems = [];
    let allUnits = {};
    let allConversions = [];
    let ingredientIndex = 0;

    $("#products-table").on("click", ".manage-recipe-btn", function () {
        const productId = $(this).data("product-id");
        const $form = $("#recipeForm");
        $form[0].reset();
        $form.attr("action", `/inventory/recipes/${productId}`);
        $("#recipe-product-name").text("Loading...");
        $("#current-ingredients-list").html("<p>Loading recipe...</p>");
        $("#ingredient-list").empty();
        loadRecipeData(productId);
        $("#recipeModal").modal("show");
    });

    function loadRecipeData(productId) {
        $.ajax({
            url: `/inventory/recipes/${productId}/data`,
            type: "GET",
            success: function (data) {
                allInventoryItems = data.allInventoryItems;
                allUnits = data.allUnits;
                allConversions = data.allConversions;
                ingredientIndex = 0;

                $("#recipe-product-name").text(data.product.name);

                const $currentList = $("#current-ingredients-list").empty();
                if (data.currentIngredients.length > 0) {
                    data.currentIngredients.forEach((ing) => {
                        const unitName = ing.base_unit_name || "";
                        $currentList.append(
                            `<li class="list-group-item d-flex justify-content-between align-items-center">${ing.item.name}<span class="badge bg-primary rounded-pill">${ing.pivot.quantity_used} ${unitName}</span></li>`
                        );
                    });
                } else {
                    $currentList.html(
                        "<p>This product does not have a recipe yet.</p>"
                    );
                }

                const $formList = $("#ingredient-list").empty();
                if (data.currentIngredients.length > 0) {
                    data.currentIngredients.forEach((ing) =>
                        addIngredientRow(ing)
                    );
                }
            },
            error: function () {
                Toast.fire("Error", "Failed to load recipe data.", "error");
            },
        });
    }

    function addIngredientRow(ingredient = null) {
        let optionsHtml = '<option value="">Select Ingredient...</option>';
        allInventoryItems.forEach((item) => {
            const isSelected =
                ingredient && ingredient.id === item.id ? "selected" : "";
            const categoryName = item.item.category_r_s
                ? item.item.category_r_s.name
                : "Uncategorized";
            optionsHtml += `<option value="${item.id}" ${isSelected} data-unit-type="${item.item.unit_r_s.type}">${item.item.name} (${categoryName})</option>`;
        });

        const rowHtml = `
        <div class="row ingredient-row mb-2">
            <div class="col-md-5"><select class="form-select ingredient-select" name="ingredients[${ingredientIndex}][id]">${optionsHtml}</select></div>
            <div class="col-md-3"><input type="number" class="form-control quantity-input" name="ingredients[${ingredientIndex}][quantity]" value="" step="0.01" placeholder="Quantity"></div>
            <div class="col-md-3"><select class="form-select unit-select" name="ingredients[${ingredientIndex}][unit_id]"></select></div>
            <div class="col-md-1"><button type="button" class="btn btn-danger remove-ingredient-btn" title="Remove">X</button></div>
        </div>`;
        $("#ingredient-list").append(rowHtml);

        if (ingredient) {
            const $newRow = $("#ingredient-list")
                .find(".ingredient-row")
                .last();
            const $unitSelect = $newRow.find(".unit-select");
            const unitType = ingredient.item.unit_r_s.type;

            populateUnitDropdown($unitSelect, unitType);

            const baseUnit = allUnits[unitType].find((u) => u.is_base_unit);
            if (baseUnit) {
                $unitSelect.val(baseUnit.id);
            }

            $newRow.find(".quantity-input").val(ingredient.pivot.quantity_used);
        }

        ingredientIndex++;
    }

    $("#ingredient-list").on("change", ".ingredient-select", function () {
        const selectedOption = $(this).find("option:selected");
        const unitType = selectedOption.data("unit-type");
        const $unitSelect = $(this)
            .closest(".ingredient-row")
            .find(".unit-select");
        populateUnitDropdown($unitSelect, unitType);
    });

    function populateUnitDropdown($selectElement, unitType) {
        $selectElement.empty();
        if (unitType && allUnits[unitType]) {
            allUnits[unitType].forEach((unit) => {
                $selectElement.append(
                    `<option value="${unit.id}">${unit.name}</option>`
                );
            });
        }
    }

    $("#recipeModal").on("click", "#add-ingredient-btn", () =>
        addIngredientRow()
    );
    $("#recipeModal").on("click", ".remove-ingredient-btn", function () {
        $(this).closest(".ingredient-row").remove();
    });

    $("#recipeForm").on("submit", function (e) {
        e.preventDefault();
        const form = $(this);
        const productId = form.attr("action").split("/").pop();
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function (response) {
                $("#recipeModal").modal("hide");
                Toast.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                });
                calculateAndSuggestPrice(productId);
                $("#products-table").DataTable().ajax.reload();
            },
            error: function (xhr) {
                Toast.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                });
            },
        });
    });

    function calculateAndSuggestPrice(productId) {
        $.get(
            `/inventory/recipes/${productId}/calculate-price`,
            function (data) {
                Swal.fire({
                    title: "Update Product Price?",
                    html: `The calculated recipe cost is <b>₱${data.total_cost}</b>.<br>
                   Based on a 30% profit margin, the suggested price is <b>₱${data.suggested_price}</b>.<br><br>
                   Do you want to update this product's base price?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Update Price",
                    cancelButtonText: "No, Keep Current Price",
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateProductPrice(productId, data.suggested_price);
                    }
                });
            }
        ).fail(function () {
            Toast.fire("Error", "Could not calculate recipe cost.", "error");
        });
    }

    function updateProductPrice(productId, newPrice) {
        $.ajax({
            url: `/inventory/products/${productId}/update-price`,
            type: "PATCH",
            data: {
                base_price: newPrice,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                Toast.fire("Updated!", response.message, "success");
                $("#products-table").DataTable().ajax.reload(null, false);
            },
            error: function () {
                Toast.fire(
                    "Error",
                    "Failed to update the product price.",
                    "error"
                );
            },
        });
    }
});
