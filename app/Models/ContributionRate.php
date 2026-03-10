<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributionRate extends Model
{
    use SoftDeletes;

    protected $table = 'contribution_rates';

    protected $fillable = [
        'sss_employee_rate',
        'sss_employer_rate',
        'philhealth_employee_rate',
        'philhealth_employer_rate',
        'pagibig_employee_rate',
        'pagibig_employer_rate',
        'effective_date',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_active'      => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
