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
        'employee_id','date','time_in','time_out','hours_worked','hours_late',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'time_in' => 'datetime:H:i:s',
        'time_out' => 'datetime:H:i:s',
        'employee_id' => 'integer',
        'hours_worked' => 'integer',
        'hours_late' => 'integer', // Keep overtime_minutes in the attendance table
    ];

    public function atEmployeeRS()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
