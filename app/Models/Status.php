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
            18 => '<span class="badge bg-warning">Approved<br>Pending Dispatch</span>',
            19 => '<span class="badge bg-danger">Rejected<br>Supplier</span>',
            20 => '<span class="badge bg-success">Accepted<br>Supplier</span>',
            21 => '<span class="badge bg-warning">Pending<br>Supplier</span>',
            22 => '<span class="badge bg-danger">Return</span>',
            null => '<span class="badge bg-secondary">Unknown</span>'
        ];
        return $statuses[$statusCode];
    }
    public static function statusAlert($statusCode){
        $statuses = [
            1 => '<div class="alert alert-success">Active</div>',
            2 => '<div class="alert alert-danger">Inactive</div>',
            3 => '<div class="alert alert-warning">Suspended</div>',
            4 => '<div class="alert alert-danger">Terminated</div>',
            5 => '<div class="alert alert-danger">Resigned</div>',
            6 => '<div class="alert alert-success">Present</div>',
            7 => '<div class="alert alert-primary">On Time</div>',
            8 => '<div class="alert alert-primary">On leave</div>',
            9 => '<div class="alert alert-warning">Late</div>',
            10 => '<div class="alert alert-danger">Absent</div>',
            11 => '<div class="alert alert-warning">Pending</div>',
            12 => '<div class="alert alert-danger">Rejected</div>',
            13 => '<div class="alert alert-success">Approved</div>',
            14 => '<div class="alert alert-info">On Process</div>',
            15 => '<div class="alert alert-success">Released</div>',
            16 => '<div class="alert alert-success">Delivered</div>',
            17 => '<div class="alert alert-warning">Partial Delivered</div>',
            18 => '<div class="alert alert-warning">Approved - Pending Dispatch</div>',
            19 => '<div class="alert alert-danger">Rejected - Supplier</div>',
            20 => '<div class="alert alert-success">Accepted - Supplier</div>',
            21 => '<div class="alert alert-warning">Pending - Supplier</div>',
            22 => '<div class="alert alert-danger">Return</div>',
            null => '<span class="badge bg-secondary">Unknown</div>'
        ];
        return $statuses[$statusCode];
    }
}
