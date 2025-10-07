<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'id',
        'order_id',
        'delivery_no',
        'total_amount',
        'date_received',
        'date_approved',
        'supplier_id',
        'approved_by_id',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    
    public function purchaseOrders(): HasMany {
        return $this->hasMany(PurchaseOrder::class,'id', 'order_id');
    }

    public function purchaseRequestRS() {
        return $this->hasOne(PurchaseRequest::class);
    }

    public function purchaseOrderDetailRS(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'purchase_order_id', 'order_id');
    }

    public function userRS(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function employeeRS(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by_id');
    }
}
