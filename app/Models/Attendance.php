<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    //
    use SoftDeletes;
    protected $table = 'attendances';
    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
        'hours_worked',
        'tardiness',
        'tardiness_minutes',
        'overtime_minutes',
        'leave_id',
        'overtime_id',
        'status',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'time_in' => 'datetime:H:i:s',
        'time_out' => 'datetime:H:i:s',
        'employee_id' => 'integer',
        'hours_worked' => 'integer',
        'tardiness' => 'integer',
        'tardiness_minutes' => 'integer',
        'overtime_minutes' => 'integer',
    ];

    public function atEmployeeRS()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function statusRS()
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function leaveRS()
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }
    public function overtimeRS()
    {
        return $this->belongsTo(Overtime::class, 'overtime_id');
    }
}
