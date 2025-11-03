<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemUnit extends Model
{
    //
    use SoftDeletes;
    protected $table = 'item_units';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'abbreviation',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
}
