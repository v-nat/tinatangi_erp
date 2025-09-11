<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    //
    use SoftDeletes;
    protected $table = "leaves";
    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by',
        'approval_date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'id');
    }
}
