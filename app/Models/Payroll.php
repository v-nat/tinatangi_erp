<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    //
    use SoftDeletes;
    protected $table = "payrolls";
    protected $primaryKey = "id";

    protected $fillable = [
        'employee_id',
        'month',
        'days_start',
        'start_date',
        'end_date',
        'payroll_date',
        'days_present',
        'total_hours_worked',
        'regular_hour_pay',
        'overtime_pay',
        'leave_pay',
        'days_absent',
        'days_absent_deduction',
        'tardiness_deduction',
        'deduction',
        'sss',
        'philhealth',
        'pagibig',
        'tax_deduction',
        'gross_pay',
        'salary_before_tax',
        'net_pay',
        'remarks',
        'proof_of_payment',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payroll_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


}
