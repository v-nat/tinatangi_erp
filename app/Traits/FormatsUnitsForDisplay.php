<?php

namespace App\Traits;

use App\Models\ItemUnit;
use App\Models\UnitConversion;

trait FormatsUnitsForDisplay
{
    /**
     * Handles simple conversions for weight and volume to the single highest unit.
     * Example: 1500 (grams) -> "1.50 KG"
     */
    public function formatSimpleQuantity(float $quantity, ItemUnit $baseUnit): string
    {
        $bestConversion = UnitConversion::where('to_unit_id', $baseUnit->id)
                                        ->orderBy('factor', 'desc')
                                        ->first();

        if ($bestConversion && $quantity >= $bestConversion->factor) {
            $convertedValue = round($quantity / $bestConversion->factor, 2);
            $higherUnit = ItemUnit::find($bestConversion->from_unit_id);
            if ($higherUnit) {
                return $convertedValue . ' ' . $higherUnit->abbreviation;
            }
        }

        // FIX: Return the original quantity in its base unit, without dividing.
        return round($quantity / 1000, 4) . ' ' . $baseUnit->abbreviation;
    }

    /**
     * Handles complex "exploding" conversions for count-based units.
     * Example: 305 (pieces) -> "3 Bags, 5 PCS"
     */
    public function formatComplexQuantity(float $quantity, ItemUnit $baseUnit): string
    {
        // Get all possible larger units, ordered from largest to smallest factor.
        $conversions = UnitConversion::where('to_unit_id', $baseUnit->id)
                                     ->orderBy('factor', 'desc')
                                     ->get();

        // Eager load the unit names for efficiency
        $unitIds = $conversions->pluck('from_unit_id')->push($baseUnit->id);
        $units = ItemUnit::whereIn('id', $unitIds)->pluck('abbreviation', 'id');

        $remainingQuantity = $quantity;
        $parts = [];

        foreach ($conversions as $conv) {
            // Skip conversions with a factor of 1, as they are equivalent to the base unit.
            if ($conv->factor <= 1) continue;

            if ($remainingQuantity >= $conv->factor) {
                $wholeUnits = floor($remainingQuantity / $conv->factor);
                $parts[] = $wholeUnits . ' ' . $units[$conv->from_unit_id];
                $remainingQuantity %= $conv->factor;
            }
        }

        // Add the final remainder in the base unit.
        if ($remainingQuantity > 0) {
            $parts[] = round($remainingQuantity, 2) . ' ' . $units[$baseUnit->id];
        }

        return !empty($parts) ? implode(', ', $parts) : '0 ' . $units[$baseUnit->id];
    }
}
