<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeSalarySettings extends Model
{
    use SoftDeletes;
    protected $table = 'employee_salary_settings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'position_id',
        'base_salary',
        'rate_per_hour',
        'rate_per_day',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}
