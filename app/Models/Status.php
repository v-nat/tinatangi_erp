<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Status extends Model
{
    //
    use HasFactory, SoftDeletes;
    protected $table = 'status';
    protected $fillable = ['status'];

    public static function getStatusText($statusCode)
    {
        $statuses = [
            7 => '<span class="badge bg-warning">Pending</span>',
            13 => '<span class="badge bg-success">Approved</span>',
            12 => '<span class="badge bg-primary">Rejected</span>',
            null => '<span class="badge bg-secondary">Unknown</span>'
        ];
        return $statuses[$statusCode];
    }
}
