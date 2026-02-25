<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseOrderDetail extends Model
{
    //
    use SoftDeletes;
    protected $table = 'purchase_order_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'category_id',
        'quantity',
        'unit_price',
        'total_amount',
        'backorder_qnty',
        'delivered_qnty',
        'sku',
        'status',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function itemss(): BelongsTo{
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    /**
     * Get the return details for this purchase order item.
     */
    public function deliveryReturn(): HasOne
    {
        return $this->hasOne(DeliveryReturn::class);
    }
}
