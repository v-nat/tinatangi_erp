<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Models\Product;
use App\Models\ItemUnit;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\UnitConversion;
use App\Http\Controllers\Controller;

class RecipeController extends Controller
{
    public function getRecipeData(Product $product)
    {
        $product->load('ingredients.item.unitRS');
        $allInventoryItems = InventoryItem::with('item.categoryRS', 'item.unitRS')->get();
        $allUnits = ItemUnit::all()->groupBy('type');
        $allConversions = UnitConversion::all();

        $formattedIngredients = $product->ingredients->map(function ($ingredient) {
            $baseUnit = ItemUnit::where('type', $ingredient->item->unitRS->type)
                ->where('is_base_unit', true)
                ->first();

            $ingredient->base_unit_name = $baseUnit ? $baseUnit->name : $ingredient->item->unitRS->name;

            return $ingredient;
        });

        return response()->json([
            'product' => $product,
            'currentIngredients' => $formattedIngredients,
            'allInventoryItems' => $allInventoryItems,
            'allUnits' => $allUnits,
            'allConversions' => $allConversions,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'ingredients' => 'nullable|array',
            'ingredients.*.id' => 'required|exists:inventory_items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit_id' => 'required|integer|exists:item_units,id',
        ]);

        $ingredientsData = $request->input('ingredients', []);
        $dataToSync = [];

        foreach ($ingredientsData as $ingredient) {
            $submittedQuantity = $ingredient['quantity'];
            $submittedUnitId = $ingredient['unit_id'];
            $inventoryItemId = $ingredient['id'];

            $submittedUnit = ItemUnit::find($submittedUnitId);
            $quantityInBaseUnit = $submittedQuantity;

            if ($submittedUnit && !$submittedUnit->is_base_unit) {
                $baseUnit = ItemUnit::where('type', $submittedUnit->type)->where('is_base_unit', true)->first();
                if ($baseUnit) {
                    $conversion = UnitConversion::where('from_unit_id', $submittedUnitId)
                        ->where('to_unit_id', $baseUnit->id)
                        ->first();
                    if ($conversion) {
                        $quantityInBaseUnit = $submittedQuantity * $conversion->factor;
                    }
                }
            }

            $dataToSync[$inventoryItemId] = ['quantity_used' => $quantityInBaseUnit];
        }

        $product->ingredients()->sync($dataToSync);

        return response()->json(['message' => 'Recipe updated successfully!']);
    }

    public function calculatePrice(Product $product)
    {
        $product->load('ingredients');

        if ($product->ingredients->isEmpty()) {
            return response()->json(['error' => 'Product has no recipe.'], 404);
        }

        $totalCost = 0;

        foreach ($product->ingredients as $ingredient) {
            $costPerBaseUnit = $ingredient->unit_cost;

            $quantityUsedInRecipe = $ingredient->pivot->quantity_used;
            $totalCost += $costPerBaseUnit * $quantityUsedInRecipe;
        }

        $profitMargin = 0.30;
        $suggestedPrice = $totalCost / (1 - $profitMargin);

        return response()->json([
            'total_cost' => round($totalCost, 2),
            'suggested_price' => round($suggestedPrice, 2),
        ]);
    }

    public function updatePrice(Request $request, Product $product)
    {
        $request->validate(['base_price' => 'required|numeric|min:0']);

        $product->base_price = $request->base_price;
        $product->save();

        return response()->json(['message' => 'Product price updated successfully!']);
    }
}
