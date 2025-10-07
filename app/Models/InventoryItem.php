<?php

namespace App\Models;

use App\Models\Status;
use App\Models\Category;
use App\Models\ItemUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    //
    use SoftDeletes;
    protected $table = 'inventory_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'sku',
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
}

