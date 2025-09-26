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
            1 => '<span class="badge bg-success">Active</span>',
            2 => '<span class="badge bg-danger">Inactive</span>',
            3 => '<span class="badge bg-warning">Suspended</span>',
            4 => '<span class="badge bg-danger">Terminated</span>',
            5 => '<span class="badge bg-danger">Resigned</span>',
            6 => '<span class="badge bg-success">Present</span>',
            7 => '<span class="badge bg-primary">On Time</span>',
            8 => '<span class="badge bg-primary">On leave</span>',
            9 => '<span class="badge bg-warning">Late</span>',
            10 => '<span class="badge bg-danger">Absent</span>',
            11 => '<span class="badge bg-warning">Pending</span>',
            12 => '<span class="badge bg-danger">Rejected</span>',
            13 => '<span class="badge bg-success">Approved</span>',
            14 => '<span class="badge bg-info">On Process</span>',
            15 => '<span class="badge bg-success">Released</span>',
            16 => '<span class="badge bg-success">Delivered</span>',
            17 => '<span class="badge bg-warning">Partial Delivered</span>',
            18 => '<span class="badge bg-warning">Approved - Pending Dispatch</span>',
            19 => '<span class="badge bg-danger">Rejected - Supplier</span>',
            20 => '<span class="badge bg-success">Accepted - Supplier</span>',
            21 => '<span class="badge bg-danger">Return</span>',
            null => '<span class="badge bg-secondary">Unknown</span>'
        ];
        return $statuses[$statusCode];
    }
}
