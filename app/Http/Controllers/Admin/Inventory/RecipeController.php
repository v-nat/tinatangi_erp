<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Http\Controllers\Controller;

class RecipeController extends Controller
{
    public function edit(Product $product)
    {
        // The only job of this method is to return the view with the product ID.
        // The JavaScript file will handle fetching all the data.
        return view('pages.admin.inventory.recipes', ['product' => $product]);
    }

    /**
     * NEW: Provide all necessary recipe data as a JSON response for AJAX calls.
     */
    public function getRecipeData(Product $product)
    {
        // Eager load the pivot data for efficiency
        $product->load('ingredients');

        return response()->json([
            'product' => $product,
            'currentIngredients' => $product->ingredients,
            'allInventoryItems' => InventoryItem::orderBy('name')->get()
        ]);
    }

    /**
     * Update the recipe for the given product in storage.
     * This method does not need to change.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'ingredients' => 'nullable|array',
            'ingredients.*.id' => 'required|exists:inventory_items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $ingredientsData = $request->input('ingredients', []);
        $dataToSync = [];

        foreach ($ingredientsData as $ingredient) {
            $dataToSync[$ingredient['id']] = ['quantity_used' => $ingredient['quantity']];
        }

        $product->ingredients()->sync($dataToSync);

        return redirect()->route('recipes.edit', $product)->with('success', 'Recipe updated successfully!');
    }
}
