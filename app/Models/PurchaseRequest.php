<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PurchaseRequest extends Model
{
    //
    use SoftDeletes;
    protected $table = 'purchase_requests';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'department',
        'type',
        'amount',
        'requested_by_id',
        'requested_date',
        'remarks',
        'invoice_id',
        'supplier_id',
        'status',
    ] ;

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

    public function deptRS(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department');
    }
    public function employeeRS(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by_id');
    }
    public function purchaseOrders(): HasMany {
        return $this->hasMany(PurchaseOrder::class,'purchase_request_id');
    }

    public function deliveryReturns(): HasManyThrough
    {
        return $this->hasManyThrough(
            DeliveryReturn::class,
            PurchaseOrderDetail::class,
            'purchase_order_id', // Foreign key on PurchaseOrderDetail table
            'purchase_order_detail_id', // Foreign key on DeliveryReturn table
            'id', // Local key on PurchaseRequest table (maps to PurchaseOrder)
            'id' // Local key on PurchaseOrderDetail table
        )->through(function ($purchaseOrderDetail) {
            // Need to join through purchase_orders to link back to purchase_request_id
            $purchaseOrderDetail->join('purchase_orders', 'purchase_order_details.purchase_order_id', '=', 'purchase_orders.id')
                                ->where('purchase_orders.purchase_request_id', $this->id);
        });
    }
}
