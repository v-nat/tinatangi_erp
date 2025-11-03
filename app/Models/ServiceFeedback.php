<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFeedback extends Model
{
    use SoftDeletes;
    protected $table = 'service_feedback';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        'message',
        'environment_rating',
        'food_rating',
        'staff_rating',
        'overall_rating',
        'photo',
        'ip_address',
        'location',
        'status',
    ];


}
