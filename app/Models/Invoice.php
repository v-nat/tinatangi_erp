<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'id',
        'order_id',
        'delivery_no',
        'total_amount',
        'date_recieved',
        'date_approved',
        'supplier_id',
        'approved_by_id',
        'status',
    ];

}
