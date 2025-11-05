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
    protected $fillable = ['id', 'status'];

    /*
    |--------------------------------------------------------------------------
    | Functions
    |--------------------------------------------------------------------------
    */

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
            23 => '<span class="badge bg-success">Completed</span>',
            24 => '<span class="badge bg-success">On Stock</span>',
            25 => '<span class="badge bg-warning">Low Stock</span>',
            26 => '<span class="badge bg-danger">Out of Stock</span>',
            27 => '<span class="badge bg-warning">Pending<br>Restock</span>',
            28 => '<span class="badge bg-warning">In Queue</span>',
            29 => '<span class="badge bg-info">In Prep</span>',
            30 => '<span class="badge bg-success">Ready</span>',
            31 => '<span class="badge bg-danger">Voided</span>',
            32 => '<span class="badge bg-danger">Loss</span>',
            33 => '<span class="badge bg-success">Growth</span>',
            34 => '<span class="badge bg-danger">Hidden</span>',
            35 => '<span class="badge bg-success">Displayed</span>',
            36 => '<span class="badge bg-success">Redeliver<br>Supplier</span>',
            37 => '<span class="badge bg-danger">Cancelled<br>Supplier</span>',
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
            23 => '<div class="alert alert-success">Completed</div>',
            24 => '<div class="alert alert-success">On Stock</div>',
            25 => '<div class="alert alert-warning">Low Stock</div>',
            26 => '<div class="alert alert-danger">Out of Stock</div>',
            27 => '<div class="alert alert-warning">Pending Restock</div>',
            28 => '<div class="alert alert-warning">In Queue</div>',
            29 => '<div class="alert alert-info">In Preparation</div>',
            30 => '<div class="alert alert-success">Ready</div>',
            31 => '<div class="alert alert-danger">Voided</div>',
            32 => '<div class="alert alert-danger">Loss</div>',
            33 => '<div class="alert alert-success">Growth</div>',
            34 => '<div class="alert alert-danger">Hidden</div>',
            35 => '<div class="alert alert-success">Displayed</div>',
            36 => '<div class="alert alert-success">Redeliver - Supplier</div>',
            37 => '<div class="alert alert-danger">Cancelled - Supplier</div>',
            null => '<span class="badge bg-secondary">Unknown</div>'
        ];
        return $statuses[$statusCode];
    }
}
