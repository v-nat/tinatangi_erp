<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Employee extends Model
{
    //
    use SoftDeletes;
    protected $table = 'employees';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'address',
        'postal_code',
        'gender',
        'birth_date',
        'age',
        'phone_number',
        'citizenship',
        'department',
        'position',
        'direct_supervisor',
        'sss',
        'pagibig',
        'philhealth',
        'salary',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'salary' => 'decimal:2',
        'sss' => 'decimal:2',
        'pagibig' => 'decimal:2',
        'philhealth' => 'decimal:2',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function departmentRS(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department');
    }
    public function directSupervisorRS(): BelongsTo {
        return $this->belongsTo(Employee::class, 'direct_supervisor');
    }
}
