<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    //
    use SoftDeletes;
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'category_id',
        'unit_id',
        'unit_price',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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
