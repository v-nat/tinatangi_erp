<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'id',
        'user_id',
        'supplier_name',
        'email',
        'phone_number',
        'status',
    ];

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
}
