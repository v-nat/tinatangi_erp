<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'table_id',
        'name',
        'email',
        'phone',
        'date',
        'time',
        'people',
        'message',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the status associated with the booking.
     */
    public function statusRS()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function tableForReservation(): BelongsTo
    {
        return $this->belongsTo(TableForReservation::class, 'table_id');
    }
    public function table(): BelongsTo
    {
        return $this->belongsTo(TableForReservation::class, 'table_id');
    }
}
