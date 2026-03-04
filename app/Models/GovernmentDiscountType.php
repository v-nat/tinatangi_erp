<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GovernmentDiscountType extends Model
{
    protected $table = 'government_discount_types';

    protected $fillable = [
        'name',
        'percentage',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'percentage' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'gov_discount_type_id');
    }
}
