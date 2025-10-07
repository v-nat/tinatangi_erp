<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    //
    use SoftDeletes;

    public function categories(): BelongsTo{
        return $this->belongsTo(Category::class);
    }

    public function scopeInCategory($query, $catId)
    {
        return $query->where('category_id', $catId);
    }

    public function unit(): BelongsTo{
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }
}
