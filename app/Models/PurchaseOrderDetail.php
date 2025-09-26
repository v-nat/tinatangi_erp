<?php

namespace App\Models;

use Database\Seeders\ItemSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderDetail extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'category_id',
        'quantity',
        'unit_price',
        'total_amount',
        'backorder_qnty',
        'delivered_qnty',
        'status',
    ];

    public function itemss(): BelongsTo{
        return $this->belongsTo(ItemSeeder::class);
    }
    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'status');
    }
}
