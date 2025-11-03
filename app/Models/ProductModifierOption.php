<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductModifierOption extends Model
{
    use SoftDeletes;

    protected $table = "product_modifier_options";
    protected $primaryKey = "id";

    protected $fillable = [
        'product_modifier_id',
        'name',
        'price_impact',
        
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function productModifierRS(): BelongsTo {
        return $this->belongsTo(ProductModifier::class, 'product_modifer_id');
    }
}
