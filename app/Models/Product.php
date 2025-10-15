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
        'status'
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function productCategoryRS(): BelongsTo {
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
}
