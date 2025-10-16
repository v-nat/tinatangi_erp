$(function () {
    // --- NEW: LOGIC FOR "MANAGE RECIPE" MODAL ---

    let allInventoryItems = []; // Global-like variable to store ingredients for dropdowns
    let ingredientIndex = 0;

    // 1. Open the modal when a "Recipe" button is clicked
    $("#products-table").on("click", ".manage-recipe-btn", function () {
        const productId = $(this).data("product-id");
        const $modal = $("#recipeModal");
        const $form = $("#recipeForm");

        // Reset form and set the action URL
        $form[0].reset();
        $form.attr("action", `/inventory/recipes/${productId}`);

        // Clear previous data and show loading state
        $("#recipe-product-name").text("Loading...");
        $("#current-ingredients-list").html("<p>Loading recipe...</p>");
        $("#ingredient-list").empty();

        // Fetch data for the modal
        loadRecipeData(productId);

        $modal.modal("show");
    });

    // 2. Function to fetch and populate recipe data
    function loadRecipeData(productId) {
        $.ajax({
            url: `/inventory/recipes/${productId}/data`,
            type: "GET",
            success: function (data) {
                allInventoryItems = data.allInventoryItems;
                ingredientIndex = 0;

                $("#recipe-product-name").text(data.product.name);

                const $currentList = $("#current-ingredients-list").empty();
                if (data.currentIngredients.length > 0) {
                    data.currentIngredients.forEach((ing) => {
                        const unitName = ing.item.unit_r_s ? ing.item.unit_r_s.name : '';
                        $currentList.append(
                            `<li class="list-group-item d-flex justify-content-between align-items-center">${ing.item.name}<span class="badge bg-primary rounded-pill">${ing.pivot.quantity_used} ${unitName}</span></li>`
                        );
                    });
                } else {
                    $currentList.html(
                        "<p>This product does not have a recipe yet.</p>"
                    );
                }

                // The rest of your function is unchanged...
                const $formList = $("#ingredient-list").empty();
                if (data.currentIngredients.length > 0) {
                    data.currentIngredients.forEach((ing) =>
                        addIngredientRow(ing)
                    );
                }
            },
            error: function () {
                Swal.fire("Error", "Failed to load recipe data.", "error");
            },
        });
    }

    // 3. Helper function to add a new row to the form
    // in public/js/inventoryProducts.js

    function addIngredientRow(ingredient = null) {
        let optionsHtml = '<option value="">Select Ingredient...</option>';

        allInventoryItems.forEach((item) => {
            const isSelected =
                ingredient && ingredient.id === item.id ? "selected" : "";

            // FIX: Access the snake_case version of your 'categoryRS' relationship
            const categoryName = item.item.category_r_s
                ? item.item.category_r_s.name
                : "Uncategorized";
            const displayText = `${item.item.name} (${categoryName})`;

            optionsHtml += `<option value="${item.id}" ${isSelected}>${displayText}</option>`;
        });

        // ... The rest of the function is unchanged ...
        const quantity = ingredient ? ingredient.pivot.quantity_used : "";
        const rowHtml = `
        <div class="row ingredient-row mb-2">
            <div class="col-md-6"><select class="form-select" name="ingredients[${ingredientIndex}][id]">${optionsHtml}</select></div>
            <div class="col-md-4"><input type="number" class="form-control" name="ingredients[${ingredientIndex}][quantity]" value="${quantity}" step="0.01" placeholder="Quantity"></div>
            <div class="col-md-2"><button type="button" class="btn btn-danger remove-ingredient-btn">Remove</button></div>
        </div>`;

        $("#ingredient-list").append(rowHtml);
        ingredientIndex++;
    }

    // 4. Event handlers for adding/removing rows and submitting the form
    $("#recipeModal").on("click", "#add-ingredient-btn", () =>
        addIngredientRow()
    );
    $("#recipeModal").on("click", ".remove-ingredient-btn", function () {
        $(this).closest(".ingredient-row").remove();
    });

    $("#recipeForm").on("submit", function (e) {
        e.preventDefault();
        const form = $(this);

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: form.serialize(),
            success: function (response) {
                $("#recipeModal").modal("hide");
                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    timer: 1500,
                });
                $("#products-table").DataTable().ajax.reload();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                });
            },
        });
    });
});
