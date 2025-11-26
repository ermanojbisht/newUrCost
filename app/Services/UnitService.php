<?php

namespace App\Services;

use App\Models\Unit;
use Illuminate\Support\Facades\Log;

class UnitService
{
    /**
     * Get the conversion factor between two units.
     * Accepts either Unit model instances or IDs.
     *
     * @param Unit|int|null $rateUnit
     * @param Unit|int|null $qtyUnit
     * @return float
     */
    public function getConversionFactor($qtyUnit,$rateUnit): float
    {
        // Resolve ID to Model (if needed)
        $rateUnit = $this->resolveUnit($rateUnit);
        $qtyUnit  = $this->resolveUnit($qtyUnit);

        // If either is missing, return default
        if (!$rateUnit || !$qtyUnit) {
            Log::warning('UnitService: Missing rateUnit or qtyUnit', [
                'rateUnit' => $rateUnit,
                'qtyUnit'  => $qtyUnit
            ]);
            return 1;
        }

        // If same unit, no conversion needed
        if ($rateUnit->id === $qtyUnit->id) {
            return 1;
        }

        // Safe conversion factor retrieval
        $rateFactor = $rateUnit->conversion_factor > 0 ? $rateUnit->conversion_factor : 1;
        $qtyFactor  = $qtyUnit->conversion_factor > 0 ? $qtyUnit->conversion_factor : 1;

        return $rateFactor / $qtyFactor;
    }

    /**
     * Resolve ID → Model OR return original if it's already a model.
     *
     * @param Unit|int|null $unit
     * @return Unit|null
     */
    private function resolveUnit($unit): ?Unit
    {
        if ($unit instanceof Unit) {
            return $unit;
        }

        if (is_numeric($unit)) {
            return Unit::find($unit);
        }

        return null;
    }
}
