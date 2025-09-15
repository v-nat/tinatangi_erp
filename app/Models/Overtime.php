<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function employeeRS()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by', 'id');
    }

    public function statusRS()
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function attendanceRS()
    {
        return $this->belongsTo(Attendance::class);
    }
    
    public function departmentRS(): BelongsTo{
        return $this->belongsTo(Department::class,'name');
    }
}
