<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use SoftDeletes;

    protected $table = "products";
    protected $primaryKey = "id";

    protected $fillable = [
        'product_category_id',
        'name',
        'base_price',
        'description',
        'image',
        'servings',
        'status'
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function productCategoryRS(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'recipe_items')
            ->withPivot('quantity_used');
    }

    /**
     * Calculate the total number of servings that can be produced based on current inventory.
     */
    public function calculateAvailableServings(): int
    {
        $ingredients = $this->relationLoaded('ingredients')
            ? $this->ingredients
            : $this->ingredients()->withPivot('quantity_used')->get();

        if ($ingredients->isEmpty()) {
            return 0;
        }

        $availableServings = $ingredients->map(function (InventoryItem $ingredient) {
            $quantityNeeded = (float) $ingredient->pivot->quantity_used;

            if ($quantityNeeded <= 0.0) {
                return INF;
            }

            $stockInBaseUnit = $ingredient->getAvailableBaseUnits();

            if ($stockInBaseUnit <= 0) {
                return 0;
            }

            return (int) floor($stockInBaseUnit / $quantityNeeded);
        })->reject(function ($value) {
            return $value === INF;
        })->min();

        if ($availableServings === null) {
            return 0;
        }

        return max(0, (int) $availableServings);
    }

    /**
     * Update the product's status based on currently available servings.
     */
    public function syncAvailability(): int
    {
        $availableServings = $this->calculateAvailableServings();
        $newStatus = $availableServings > 0 ? 1 : 26;

        if ($this->status !== $newStatus) {
            $this->status = $newStatus;
            $this->save();
        }

        return $availableServings;
    }
}
