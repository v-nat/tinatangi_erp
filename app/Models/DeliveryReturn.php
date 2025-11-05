<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_detail_id',
        'reason',
        'photo_path',
    ];

    /**
     * Get the purchase order detail that this return belongs to.
     */
    public function purchaseOrderDetail(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderDetail::class);
    }
}
