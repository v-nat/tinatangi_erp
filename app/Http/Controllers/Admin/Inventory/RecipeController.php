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
        $product->setAttribute('available_servings', $product->calculateAvailableServings());
        $allInventoryItems = InventoryItem::with('item.categoryRS', 'item.unitRS', 'unit')->get(); // Eager load item unit
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
            'ingredients' => 'required|array|min:1',
            'ingredients.*.id' => 'required|exists:inventory_items,id',
            'ingredients.*.quantity' => 'required|numeric|min:0.01',
            'ingredients.*.unit_id' => 'required|integer|exists:item_units,id',
        ]);

        $ingredientsData = $request->input('ingredients', []);
        $dataToSync = [];
        $previousIngredientIds = $product->ingredients()->pluck('inventory_items.id')->all();

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

        $product->load('ingredients');
        $availableServings = $product->syncAvailability();
        $product->servings = $availableServings;
        $product->save();

        $affectedInventoryIds = array_unique(array_merge(
            $previousIngredientIds,
            array_keys($dataToSync)
        ));

        if (! empty($affectedInventoryIds)) {
            InventoryItem::whereIn('id', $affectedInventoryIds)
                ->get()
                ->each
                ->refreshProductAvailability();
        }

        return response()->json([
            'message' => 'Recipe updated successfully!',
            'available_servings' => $availableServings,
        ]);
    }
}
