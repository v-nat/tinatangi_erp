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
        'table_number',
        'name',
        'email',
        'phone',
        'date',
        'time',
        'duration_hours',
        'arrived_at',
        'people',
        'message',
        'status',
        'status_note',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date'       => 'date',
        'arrived_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'status_badge',
        'status_label',
    ];

    /**
     * Get a badge representation of the booking status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return Status::getStatusText($this->status);
    }

    /**
     * Get the plain-text label of the booking status.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->statusRS?->status ?? 'Unknown';
    }

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
