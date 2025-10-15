$(function () {
    const productId = $("#recipe-editor").data("product-id");
    if (!productId) {
        console.error("Product ID not found.");
        return;
    }

    let allInventoryItems = []; // To store all available ingredients for dropdowns
    let ingredientIndex = 0; // To ensure unique names for form inputs

    // --- Main function to fetch data and build the page ---
    function loadRecipeData() {
        $.ajax({
            url: `/api/recipes/${productId}/data`,
            type: "GET",
            dataType: "json",
            success: function (data) {
                allInventoryItems = data.allInventoryItems; // Store for later

                // Populate product name
                $("#product-name").text(data.product.name);

                // Populate current ingredients list (read-only view)
                const $currentList = $("#current-ingredients-list");
                $currentList.empty(); // Clear "Loading..." message
                if (data.currentIngredients.length > 0) {
                    data.currentIngredients.forEach((ing) => {
                        const listItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
                            ${ing.name}
                            <span class="badge bg-primary rounded-pill">${ing.pivot.quantity_used} ${ing.unit_of_measure}</span>
                        </li>`;
                        $currentList.append(listItem);
                    });
                } else {
                    $("#no-recipe-message")
                        .text("This product does not have a recipe yet.")
                        .appendTo($currentList);
                }

                // Populate the editable form rows
                const $formList = $("#ingredient-list");
                $formList.empty();
                if (data.currentIngredients.length > 0) {
                    data.currentIngredients.forEach((ing) => {
                        addIngredientRow(ing);
                    });
                }
            },
            error: function () {
                alert("Failed to load recipe data. Please try again.");
            },
        });
    }

    // --- Helper function to create a new ingredient row in the form ---
    function addIngredientRow(ingredient = null) {
        let optionsHtml = '<option value="">Select Ingredient...</option>';
        allInventoryItems.forEach((item) => {
            // Check if the current item should be selected
            const isSelected =
                ingredient && ingredient.id === item.id ? "selected" : "";
            optionsHtml += `<option value="${item.id}" ${isSelected}>${item.name} (${item.unit_of_measure})</option>`;
        });

        const quantity = ingredient ? ingredient.pivot.quantity_used : "";

        const ingredientRowHtml = `
            <div class="row ingredient-row mb-2">
                <div class="col-md-6">
                    <select class="form-select" name="ingredients[${ingredientIndex}][id]">${optionsHtml}</select>
                </div>
                <div class="col-md-4">
                    <input type="number" class="form-control" name="ingredients[${ingredientIndex}][quantity]" value="${quantity}" step="0.01" placeholder="Quantity">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-ingredient-btn">Remove</button>
                </div>
            </div>`;

        $("#ingredient-list").append(ingredientRowHtml);
        ingredientIndex++;
    }

    // --- Event Handlers ---
    $("#add-ingredient-btn").click(function () {
        addIngredientRow(); // Add a new, blank row
    });

    $("#ingredient-list").on("click", ".remove-ingredient-btn", function () {
        $(this).closest(".ingredient-row").remove();
    });

    // --- Initial Load ---
    loadRecipeData();
});
