<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryLocation extends Model
{
    //
    use SoftDeletes;
    protected $table = 'inventory_locations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
}
