<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    //
    use SoftDeletes;

    protected $table = 'stock_transactions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'transaction_type',
        'quantity',
        'old_qnty',
        'batch',
        'reference_type',
        'reference_id',
        'user_id',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // 'transaction_date' is a timestamp in the DB, but can be managed by Laravel
        'transaction_type' => \App\Enums\TransactionType::class,
        'transaction_date' => 'datetime',
        'quantity' => 'decimal:3', // Ensures quantity is cast as a float/decimal with 3 places
    ];
    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function itemss(): BelongsTo{
        return $this->belongsTo(Item::class, 'reference_id');
    }
    public function inventoryItemRS(): BelongsTo{
        return $this->belongsTo(InventoryItem::class, 'reference_id');
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }
}
