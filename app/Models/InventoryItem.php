<?php

namespace App\Models;

use App\Models\Status;
use App\Models\Category;
use App\Models\ItemUnit;
use App\Models\UnitConversion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InventoryItem extends Model
{
    //
    use SoftDeletes;
    protected $table = 'inventory_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'sku',
        'item_id',
        'inventory_location_id',
        'unit_id',
        'category_id',
        'cost_price',
        'selling_price',
        'stock_level',
        'status',
        'expiration_date',
    ];

    /**
     * The attributes that should be cast to native types.
     * Ensures prices are treated as floating point numbers.
     *
     * @var array
     */
    protected $casts = [
        'cost_price' => 'float',
        'selling_price' => 'float',
        'stock_level' => 'float',
        'base_unit_stock_level' => 'float',
        'status' => 'integer',
        'expiration_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function itemss(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class, 'unit_id');
    }
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(ItemUnit::class, 'base_unit_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function itemStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public function inventoryLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'recipe_items')
            ->withPivot('quantity_used');
    }

    /**
     * Retrieve the preferred ItemUnit for stock display (base unit if available).
     */
    public function getDisplayUnitModel(): ?ItemUnit
    {
        if ($this->relationLoaded('unit') || $this->unit_id) {
            return $this->unit ?? $this->unit()->first();
        }

        if ($this->relationLoaded('baseUnit') || $this->base_unit_id) {
            return $this->baseUnit ?? $this->baseUnit()->first();
        }

        return null;
    }

    /**
     * Get the unit label (abbreviation fallback to name) for display.
     */
    public function getDisplayUnitLabel(): string
    {
        $unit = $this->getDisplayUnitModel();

        if (! $unit) {
            return '';
        }

        return $unit->abbreviation ?: $unit->name ?: '';
    }

    /**
     * Format the quantity (in base units) with fixed decimals.
     */
    public function formatStockQuantity(int $decimals = 2): string
    {
        return number_format($this->getDisplayQuantity(), $decimals);
    }

    /**
     * Provide a single string combining quantity and unit for display.
     */
    public function formatStockDisplay(int $decimals = 2): string
    {
        $quantity = $this->formatStockQuantity($decimals);
        $unitLabel = $this->getDisplayUnitLabel();

        return trim($quantity . ($unitLabel ? ' ' . $unitLabel : ''));
    }

    /**
     * Get the quantity expressed in the item's original unit if available.
     */
    public function getDisplayQuantity(): float
    {
        if (! is_null($this->stock_level)) {
            return (float) $this->stock_level;
        }

        $conversionFactor = $this->getBaseConversionFactor();

        if ($conversionFactor && $conversionFactor > 0) {
            return (float) $this->getAvailableBaseUnits() / $conversionFactor;
        }

        return $this->convertBaseToDisplayQuantity($this->getAvailableBaseUnits());
    }

    /**
     * Convert a base-unit quantity into the item's display unit quantity.
     */
    public function convertBaseToDisplayQuantity(float $baseQuantity): float
    {
        $conversionFactor = $this->getBaseConversionFactor();

        if ($conversionFactor && $conversionFactor > 0 && $this->unit_id !== $this->base_unit_id) {
            return $baseQuantity / $conversionFactor;
        }

        return $baseQuantity;
    }

    /**
     * Retrieve the conversion factor that converts the stored unit into the base unit.
     */
    protected function getBaseConversionFactor(): ?float
    {
        if (! $this->unit_id || ! $this->base_unit_id || $this->unit_id === $this->base_unit_id) {
            return 1.0;
        }

        static $conversionCache = [];
        $cacheKey = $this->unit_id . '-' . $this->base_unit_id;

        if (! array_key_exists($cacheKey, $conversionCache)) {
            $conversion = UnitConversion::where('from_unit_id', $this->unit_id)
                ->where('to_unit_id', $this->base_unit_id)
                ->first();

            $conversionCache[$cacheKey] = $conversion ? (float) $conversion->factor : null;
        }

        return $conversionCache[$cacheKey];
    }

    /**
     * Get the current available quantity translated into the item's base unit.
     */
    public function getAvailableBaseUnits(): float
    {
        if (! is_null($this->base_unit_stock_level)) {
            return (float) $this->base_unit_stock_level;
        }

        $conversionFactor = $this->getBaseConversionFactor();

        if ($conversionFactor && $conversionFactor > 0) {
            return (float) $this->stock_level * $conversionFactor;
        }

        return (float) $this->stock_level;
    }

    /**
     * Persist the provided base-unit quantity, keeping the tracked stock level in sync.
     */
    public function setBaseStockFromQuantity(float $baseQuantity): void
    {
        $baseQuantity = max(0.0, $baseQuantity);
        $this->base_unit_stock_level = $baseQuantity;

        $conversionFactor = $this->getBaseConversionFactor();

        if ($conversionFactor && $conversionFactor > 0 && $this->unit_id !== $this->base_unit_id) {
            $this->stock_level = $baseQuantity / $conversionFactor;
        } else {
            $this->stock_level = $baseQuantity;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Expiration Helpers
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expiration_date !== null && now()->gt($this->expiration_date);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiration_date !== null
            && !$this->isExpired()
            && now()->diffInDays($this->expiration_date, false) <= $days;
    }

    public function daysUntilExpiry(): ?int
    {
        return $this->expiration_date !== null
            ? (int) now()->diffInDays($this->expiration_date, false)
            : null;
    }

    /**
     * Recalculate availability for all products that use this inventory item.
     */
    public function refreshProductAvailability(): void
    {
        $this->loadMissing('products.ingredients');

        foreach ($this->products as $product) {
            $product->syncAvailability();
        }
    }
}
