<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecipe extends Model
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function productRS(): BelongsTo{
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function inventoryItemRS(): BelongsTo{
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}
