<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReport extends Model
{
    use SoftDeletes;

    protected $table = 'sales_reports';

    protected $fillable = [
        'order_id',
        'total_amount',
        'status',
        'reported_by',
        'reported_at',
        'reviewed_by',
        'reviewed_at',
        'remarks',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'reported_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function orderRS(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function reporterRS(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function reviewerRS(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
}

