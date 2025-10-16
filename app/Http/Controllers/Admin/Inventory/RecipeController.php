<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Http\Controllers\Controller;

class RecipeController extends Controller
{
    // The getRecipeData() method is unchanged and works perfectly.
    public function getRecipeData(Product $product)
    {
        // Load all nested relationships for existing ingredients
        $product->load('ingredients.item.categoryRS', 'ingredients.item.unitRS');

        // Eager load all nested relationships for the dropdown
        $allInventoryItems = InventoryItem::with('item.categoryRS', 'item.unitRS')->get();

        return response()->json([
            'product' => $product,
            'currentIngredients' => $product->ingredients,
            'allInventoryItems' => $allInventoryItems
        ]);
    }

    // UPDATE this method to return JSON
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

        // Return a JSON response instead of a redirect
        return response()->json(['message' => 'Recipe updated successfully!']);
    }
}
