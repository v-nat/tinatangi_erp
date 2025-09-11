<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Overtime extends Model
{
    //
    use SoftDeletes;
    protected $table = 'overtimes';

    protected $fillable = [
        'employee_id',
        'date',
        'time_start',
        'end_time',
        'total_minutes',
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
