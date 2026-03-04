<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'title',
        'content',
        'badge_text',
        'icon',
        'theme',
        'bg_style',
        'discount_type',
        'discount_value',
        'min_spend',
        'valid_from',
        'valid_until',
        'display_order',
        'status',
        'applicable_to',
        'usage_limit',
        'usage_count',
    ];

    protected $appends = ['status_html'];

    public function getStatusHtmlAttribute()
    {
        return Status::getStatusText($this->status);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
