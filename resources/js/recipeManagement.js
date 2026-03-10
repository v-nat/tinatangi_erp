import $ from 'jquery';
import Swal from 'sweetalert2';

$(function () {
    let allInventoryItems = [];
    let allUnits = {};
    let allConversions = [];
    let ingredientIndex = 0;
    const $servingsField = $("#servings_per_recipe");
    let servingsFieldName = null;

    if ($servingsField.length) {
        servingsFieldName = $servingsField.attr("name");
        $servingsField
            .attr("readonly", true)
            .removeAttr("required")
            .data("original-name", servingsFieldName)
            .attr("name", "")
            .addClass("bg-light");
    }

    $("#products-table").on("click", ".manage-recipe-btn", function () {
        const productId = $(this).data("product-id");
        const $form = $("#recipeForm");
        $form[0].reset();
        $form.attr("action", `/inventory/recipes/${productId}`);
        $("#recipe-product-name").text("Loading...");
        $("#current-ingredients-list").html("<p>Loading recipe...</p>");
        $("#ingredient-list").empty();
        if ($servingsField.length) {
            $servingsField.val("Calculating...");
        }
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
                if ($servingsField.length) {
                    const availableServings =
                        data.product.available_servings ??
                        data.product.servings ??
                        0;
                    $servingsField.val(availableServings);
                }

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
            const inventoryItem = item.item || {};
            const categoryRelation = inventoryItem.category || {};
            const unitRelation = inventoryItem.unit || item.unit || {};
            const unitType = unitRelation ? unitRelation.type : null;

            const isSelected =
                ingredient && ingredient.id === item.id ? "selected" : "";
            const categoryName = categoryRelation.name || "Uncategorized";
            optionsHtml += `<option value="${item.id}" ${isSelected} data-unit-type="${unitType || ""}">${inventoryItem.name || item.name || "Unnamed"} (${categoryName})</option>`;
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
            const inventoryItem = ingredient.item || {};
            const unitType = inventoryItem.unit ? inventoryItem.unit.type : null;

            populateUnitDropdown($unitSelect, unitType);
            if (unitType && allUnits[unitType]) {
                const baseUnit = allUnits[unitType].find((u) => u.is_base_unit);
                if (baseUnit) {
                    $unitSelect.val(baseUnit.id);
                }
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
        let isValid = true;
        form.find('input[required], select[required]').each(function() {
            if (!$(this).val() || ($(this).is('input[type="number"]') && $(this).val() <= 0)) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
             Toast.fire({
                icon: "error",
                title: "Error",
                text: "Please fill out all required fields.",
            });
            return;
        }
        const ingredientCount = $("#ingredient-list .ingredient-row").length;
        if (ingredientCount === 0) {
            Toast.fire({
                icon: "error",
                title: "Empty Recipe",
                text: "Please add at least one ingredient to the recipe.",
            });
            return; 
        }
        $("#LoadingScreen").fadeIn(200);
        const productId = form.attr("action").split("/").pop();
        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function (response) {
                $("#recipeModal").modal("hide");
                $("#LoadingScreen").fadeOut(200);
                Toast.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                });
                $("#products-table").DataTable().ajax.reload();
                if ($servingsField.length && response.available_servings !== undefined) {
                    $servingsField.val(response.available_servings);
                }
            },
            error: function (xhr) {
                $("#LoadingScreen").fadeOut(200);
                 if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let errorMsg = "Please check your inputs.\n";
                    $.each(errors, function(key, value) {
                        $(`[name="${key}"]`).addClass('is-invalid');
                        if(key.startsWith('ingredients.')) {
                             $(`[name^="ingredients[${key.split('.')[1]}]"]`).addClass('is-invalid');
                        }
                        errorMsg += value[0] + "\n";
                    });
                     Toast.fire({ icon: "error", title: "Validation Error", text: "Please check the form fields." });
                } else {
                    Toast.fire({
                        icon: "error",
                        title: "Error",
                        text: "An error occurred. Please try again.",
                    });
                }
            },
        });
    });

});
