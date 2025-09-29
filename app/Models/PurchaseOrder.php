<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'type',
        'purchase_request_id',
        'purchase_order_name',
        'purchase_order_id',
        'order_date',
        'expected_delivery_date',
        'delivery_date',
        'delivery_name',
        'remarks',
        'created_by',
        'supplier_id',
        'status',
    ] ;

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'status');
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'statforeignKey: us');
    }
}
