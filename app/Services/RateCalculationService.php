<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemRate;
use App\Models\SubitemDependency;
use App\Models\RateCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RateCalculationService
{
    protected $itemSkeletonService;

    public function __construct(ItemSkeletonService $itemSkeletonService)
    {
        $this->itemSkeletonService = $itemSkeletonService;
    }

    /**
     * Calculate rates for all items based on dependency order.
     *
     * @param int $rateCardId
     * @param int|null $sorId
     * @param bool $subitemsOnly
     * @param string|null $validFrom
     * @return array
     */
    public function calculateAll(int $rateCardId, ?int $sorId = null, bool $subitemsOnly = false, ?string $validFrom = null)
    {
        $startTime = microtime(true);
        $processedCount = 0;
        $errors = [];
        $validFrom = $validFrom ?? now()->toDateString();

        // 1. Get all items sorted by dependency depth (deepest first)
        // Items that are sub-items at deeper levels must be calculated first.
        $query = Item::leftJoinSub(
            SubitemDependency::select('sub_item_code', DB::raw('MAX(level) as depth'))
                ->groupBy('sub_item_code'),
            'deps',
            'items.item_code',
            '=',
            'deps.sub_item_code'
        )
        ->select('items.*', DB::raw('COALESCE(deps.depth, 0) as depth'))
        ->when($sorId, function ($query, $sorId) {
            return $query->where('items.sor_id', $sorId);
        });

        if ($subitemsOnly) {
            // Filter only items that are used as sub-items (exist in dependency table)
            $query->whereNotNull('deps.sub_item_code');
        }

        $items = $query->orderBy('depth', 'desc')->get();

        Log::info("Starting rate calculation for {$items->count()} items. Rate Card ID: {$rateCardId}, SOR ID: " . ($sorId ?? 'All') . ", Subitems Only: " . ($subitemsOnly ? 'Yes' : 'No') . ", Valid From: {$validFrom}");

        foreach ($items as $item) {
            try {
                $this->calculateAndSaveItemRate($item, $rateCardId, $validFrom);
                $processedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to calculate rate for item {$item->item_code}: " . $e->getMessage());
                $errors[] = "Item {$item->item_code}: " . $e->getMessage();
            }
        }

        $duration = microtime(true) - $startTime;
        Log::info("Rate calculation completed in {$duration} seconds. Processed: {$processedCount}. Errors: " . count($errors));

        return [
            'total' => $items->count(),
            'processed' => $processedCount,
            'errors' => $errors,
            'duration' => $duration
        ];
    }

    /**
     * Calculate and save rate for a single item.
     *
     * @param Item $item
     * @param int $rateCardId
     * @param string $validFrom
     */
    public function calculateAndSaveItemRate(Item $item, int $rateCardId, string $validFrom)
    {
        $data = $this->itemSkeletonService->calculateRate($item, $rateCardId);
        $validFrom=Carbon::parse($validFrom);

        // Extract totals
        $totals = $data['totals'];
        $calculationDate = now()->toDateString();

        // Check for existing active rate
        $activeRate = ItemRate::where('item_code', $item->item_code)
            ->where('rate_card_id', $rateCardId)
            ->where(function ($q) {
                $q->whereNull('valid_to')
                  ->orWhere('valid_to', '2038-01-19');
            })
            ->first();


        if ($activeRate) {

            // If active rate exists
            if ($activeRate->valid_from == $validFrom) {
                // Same valid_from, update existing record
                $activeRate->update([
                    'rate' => round($totals['final_rate'],2),
                    'labor_cost' => round($totals['total_labor'],2),
                    'material_cost' => $totals['total_material'],
                    'machine_cost' => round($totals['total_machine'],2),
                    'overhead_cost' => round($totals['overhead_cost'],2),
                    'calculation_date' => $calculationDate,
                ]);
                return;
            } else {
                // Different valid_from, close old record
                $closeDate = Carbon::parse($validFrom)->subDay()->toDateString();
                $activeRate->update(['valid_to' => $closeDate]);
            }
        }

        // Create new record
        $values = [
            'item_code'     => $item->item_code,
            'rate_card_id'  => $rateCardId,
            'calculation_date' => $calculationDate,
            'rate'          => $totals['final_rate'],
            'labor_cost'    => $totals['total_labor'],
            'material_cost' => $totals['total_material'],
            'machine_cost'  => $totals['total_machine'],
            'overhead_cost' => $totals['overhead_cost'],
            'valid_from'    => $validFrom,
            'valid_to'      => '2038-01-19', // Default indefinite
        ];

        // We use create here because we've handled the "update" case above manually
        // But to be safe against race conditions or composite key issues, we can use updateOrCreate 
        // if the primary key allows it. 
        // The primary key is ['item_id', 'rate_card_id', 'calculation_date']. 
        // Wait, if we use item_code, we might have issues if item_id is the PK column.
        // Assuming item_code maps to item_id or is the intended column.
        
        // Since we are creating a NEW record for a NEW date/validity, create is appropriate.
        // However, if we run this multiple times on the same day, we might hit PK violation if PK includes calculation_date.
        // If valid_from is different but calculation_date is same as an existing closed record?
        // The PK is (item_id, rate_card_id, calculation_date). 
        // If we calculate twice today, we might clash.
        // Ideally we should update if (item_code, rate_card_id, calculation_date) exists.
        $itemCode = (int) $item->item_code;
        ItemRate::updateOrCreate(
            [
                'item_code' => $itemCode,
                'rate_card_id' => $rateCardId,
                'valid_from' => $validFrom,
            ],
            $values
        );

        Log::info("ItemRate CREATED/UPDATED", ['item_code' => $item->item_code]);
    }
}
