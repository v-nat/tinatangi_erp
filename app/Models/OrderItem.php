<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $table = "order_items";
    protected $primaryKey = "id";

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orderRS(): BelongsTo {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function productRS(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
