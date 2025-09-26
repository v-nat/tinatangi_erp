<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    //
    use SoftDeletes;

    public function categories(){
        return $this->belongsTo(Category::class);
    }

    public function scopeInCategory($query, $catId)
    {
        return $query->where('category_id', $catId);
    }
}
