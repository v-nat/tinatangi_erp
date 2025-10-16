<?php

namespace App\Models;

use App\Models\Status;
use App\Models\Category;
use App\Models\ItemUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InventoryItem extends Model
{
    //
    use SoftDeletes;
    protected $table = 'inventory_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'sku',
        'item_id',
        'inventory_location_id',
        'unit_id',
        'category_id',
        'cost_price',
        'selling_price',
        'stock_level',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     * Ensures prices are treated as floating point numbers.
     *
     * @var array
     */
    protected $casts = [
        'cost_price' => 'float',
        'selling_price' => 'float',
        'stock_level' => 'integer',
        'status' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function itemss(): BelongsTo{
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function itemStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function inventoryLocation(): BelongsTo {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'recipe_items')
                    ->withPivot('quantity_used');
    }
}

