<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableLocation extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function tables(): HasMany
    {
        return $this->hasMany(TableForReservation::class, 'table_location_id');
    }
}
