<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'type',
        'purchase_request_id',
        'purchase_orderId',
        'order_date',
        'expected_delivery_date',
        'delivery_date',
        'delivery_name',
        'remarks',
        'created_by_id',
        'supplier_id',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function supplierRS(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function employeeRS(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_id');
    }
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }
    public function purchaseOrderDetail(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id', 'id');
    }
    public function purchaseOrderDetailRS(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id', 'order_id');
    }
}
