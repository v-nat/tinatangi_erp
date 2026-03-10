<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalkInOccupancy extends Model
{
    protected $fillable = [
        'table_id',
        'table_number',
        'occupied_at',
        'freed_at',
    ];

    protected $casts = [
        'occupied_at' => 'datetime',
        'freed_at'    => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(TableForReservation::class, 'table_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('freed_at');
    }
}
