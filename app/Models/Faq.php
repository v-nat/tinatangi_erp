<?php

namespace App\Models;

use App\Models\Status; // Import the Status model
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question',
        'answer',
        'status',
        'order',
    ];

    protected $appends = ['status_html'];

    public function getStatusHtmlAttribute()
    {
        return Status::getStatusText($this->status);
    }
}
