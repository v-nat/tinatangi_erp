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
            11 => '<span class="badge bg-warning">Pending</span>',
            12 => '<span class="badge bg-danger">Rejected</span>',
            13 => '<span class="badge bg-success">Approved</span>',
            14 => '<span class="badge bg-info">On Process</span>',
            15 => '<span class="badge bg-success">Released</span>',
            null => '<span class="badge bg-secondary">Unknown</span>'
        ];
        return $statuses[$statusCode];
    }
}
