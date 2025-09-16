<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    //
    protected $table = "payrolls";

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
        'days_absent',
        'days_absent_deduction',
        'tardiness_deduction',
        'deduction',
        'tax_deduction',
        'gross_pay',
        'salary_before_tax',
        'net_pay',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payroll_date' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


}
