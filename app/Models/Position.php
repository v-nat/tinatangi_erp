<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Position extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['name', 'level', 'department_id'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function scopeInDepartment($query, $deptId)
    {
        return $query->where('department_id', $deptId);
    }
}
