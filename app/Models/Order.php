<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use SoftDeletes;

    protected $table = "orders";
    protected $primaryKey = "id";

    protected $fillable = [
        'id',
        'order_id',
        'user_id',
        'total_amount',
        'status',
        'order_type',
        'payment_method',
        'payment_status',
        'discount_id',
        'discount_amount',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function orderItemsRS(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function userRS(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusRS(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function salesReport(): HasOne
    {
        return $this->hasOne(SalesReport::class, 'order_id');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Announcement::class, 'discount_id');
    }
}
