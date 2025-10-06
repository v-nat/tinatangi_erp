<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    //
    use SoftDeletes;
    protected $primaryKey = 'id';
    protected $table = 'purchase_requests';
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
}
