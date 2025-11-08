<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'capacity',
        'status',
    ];
    
}
