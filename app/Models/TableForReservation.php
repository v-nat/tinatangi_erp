<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableForReservation extends Model
{
    use SoftDeletes;

    protected $table = 'table_for_reservations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'image',
        'table_location_id',
        'capacity',
        'quantity',
        'status',
    ];

    protected $appends = ['location'];

    public function getLocationAttribute(): ?string
    {
        return $this->tableLocation?->name;
    }

    public function tableLocation(): BelongsTo
    {
        return $this->belongsTo(TableLocation::class, 'table_location_id');
    }
}
