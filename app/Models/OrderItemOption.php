<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemOption extends Model
{
    use SoftDeletes;

    protected $table = "order_item_options";
    protected $primaryKey = "id";

    protected $fillable = [
        'order_item_id',
        'product_modifier_option_id',
        'price'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orderItemRS(): BelongsTo {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
    public function productModifierOptRS(): BelongsTo {
        return $this->belongsTo(ProductModifierOption::class, 'product_modifier_option_id');
    }
}
