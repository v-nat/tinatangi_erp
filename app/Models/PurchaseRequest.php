<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequest extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'department',
        'type',
        'amount',
        'requested_by_id',
        'requested_date',
        'remarks',
        'status',
    ] ;

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function employeeRS(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by_id');
    }
}
