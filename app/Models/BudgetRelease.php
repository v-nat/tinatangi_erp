<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class BudgetRelease extends Model
{
    //
    use SoftDeletes;

    protected $table = "budget_releases";

    protected $fillable = [
        'release_id',
        'type',
        'amount',
        'request_id',
        'requested_by_id',
        'requested_at',
        'released_by_id',
        'released_at',
        'department',
        'notes',
        'status',
    ] ;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function employeeRS(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by_id');
    }
    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
    public function departmentRS(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department');
    }
}
