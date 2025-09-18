<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    //
    use SoftDeletes;

    public function getNameAttribute(): string
    {
        return trim($this->attributes['name']);
    }
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department');
    }
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
    public function employeesHMT(): HasManyThrough
    {
        return $this->hasManyThrough(Employee::class, Position::class);
    }
}
