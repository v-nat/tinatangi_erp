<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRecipe extends Model
{
    use SoftDeletes;

    protected $table = "product_recipes";
    protected $primaryKey = "id";

    protected $fillable = [
        'product_id',
        'inventory_item_id',
        'quantity_used',
        'status',
    ];

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

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
}
