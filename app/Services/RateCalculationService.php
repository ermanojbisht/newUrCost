<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Item;
use App\Models\ItemRate;
use App\Models\RateCard;
use App\Models\Resource;
use App\Models\Skeleton;
use App\Models\Sor;
use App\Models\SubitemDependency;
use App\Services\ItemSkeletonService;
use App\Services\ItemSubitemService;
use App\Services\OverheadService;
use App\Services\RateAnalysisService;
use App\Services\UnitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RateCalculationService
{
    protected $itemSkeletonService;

    protected $rateAnalysisService;
    protected $overheadService;
    protected $unitService;
    protected $itemSubitemService;

    public function __construct(ItemSkeletonService $itemSkeletonService,RateAnalysisService $rateAnalysisService,
        OverheadService $overheadService,
        UnitService $unitService, ItemSubitemService $itemSubitemService)
    {
        $this->itemSkeletonService = $itemSkeletonService;
        $this->rateAnalysisService = $rateAnalysisService;
        $this->overheadService = $overheadService;
        $this->unitService = $unitService;
        $this->itemSubitemService = $itemSubitemService;
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
        $items = Item::query()
                ->withDepth() //must call subitemsOnly() after withDepth()
                ->forSor($sorId)
                ->subitemsOnly($subitemsOnly)// Filter only items that are used as sub-items (exist in dependency table)
                ->orderByDesc('depth')
                ->get();

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
     * This method is the core worker that gets called for every single item in the calculateAll loop.

        1. Delegate the Calculation: It doesn't do the complex math itself. It calls our expert chef, the itemSkeletonService, and says, "Calculate the rate for this item."
        2. Handle the Price Tag (`ItemRate`): This is its most important job. After getting the final rate from the chef, it needs to put a price tag on the item in the database. It's very careful about this:
       * Check for Existing Price: It first looks to see if there's already an active price tag for this item.
           * Case A: Updating Today's Price: If a price tag for today already exists, it just updates it with the new rate.
           * Case B: A New Price for the Future: If the old price is from yesterday and we're creating a new price for today, it puts an "end date" on the old price tag and creates a brand new,
             active price tag. This way, you have a perfect history of price changes.
           * Case C: No Price Exists: If it's a brand new item, it simply creates a new price tag.
        3. Save to Database: It uses updateOrCreate to safely save this new or updated price tag information to the item_rates table.
     */
    public function calculateAndSaveItemRate(Item $item, int $rateCardId, string $validFrom)
    {
        $data = $this->itemSkeletonService->calculateRate($item, $rateCardId);
        $validFrom=Carbon::parse($validFrom);

        // Extract totals
        $totals = $data['totals'];
        $calculationDate = now()->toDateString();

        // Check for existing active rate
        $activeRate =ItemRate::fetchActiveFor($item->item_code,$rateCardId,$calculationDate,false);//may be null or ItemRate


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

    /**
     * Returns a flattened array of all Items required for a top-level analysis,
     * ordered from the deepest child to the top-level parent.
     */
    public function getAnalysisOrder(Item $item): array
    {
        /*$orderedList = [];
        $this->buildAnalysisOrder($item, $orderedList);
        Log::info("orderedList = ".print_r($orderedList,true));
        return $orderedList;*/
        return $this->itemSubitemService->getSubitems($item->item_code,true);
    }

    /**
     * Recursive helper to perform a post-order traversal of the item dependency tree.
     */
    private function buildAnalysisOrder(Item $item, array &$list)
    {
        // First, recurse through all children
        foreach ($item->subitems as $subItemRelation) {
            $this->buildAnalysisOrder($subItemRelation->subItem, $list);
        }

        // Then, add the parent item to the list if it's not already there.
        // This ensures children are always in the list before their parents.
        // We check by ID to avoid duplication
        $exists = false;
        foreach ($list as $existingItem) {
            if ($existingItem->id === $item->id) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $list[] = $item;
        }
    }
    
    /**
     * Gathers all unique resources from a list of items.
     */
    public function getUniqueResourcesForItems(array $items): array
    {
        $itemCodes = collect($items)->pluck('item_code')->unique()->values()->all();

        // one query to skeletons to get unique resource_ids
        $resourceIds = Skeleton::whereIn('item_code', $itemCodes)
            ->distinct()
            ->pluck('resource_id')
            ->filter() // in case of nulls
            ->values()
            ->all();

        // one query to resources to fetch them
        $resources = Resource::whereIn('id', $resourceIds)->get()->all();

        return $resources;
    }


    private function getUniqueResourcesForItemsWithRates(array $items, RateCard $rateCard, $date)
    {
        $resourcesMap = [];
        $resources=$this->getUniqueResourcesForItems($items);
        foreach ($resources as $resource) {
            $rateDetails = $this->rateAnalysisService->getResourceRateDetails($resource, $rateCard, $date);
            $resourcesMap[$resource->id] = [
                'id' => $resource->id,
                'resCode' => $resource->secondary_code ?? $resource->id, // Use secondary_code if available
                'name' => $resource->name,
                'unit' => $resource->unit ? $resource->unit->name : '',
                'rate' => $rateDetails['total_rate'],
                'resource' => $resource
            ];
        }

        return array_values($resourcesMap);
    }








    /**
     * The main public method to orchestrate data preparation for the export.
     */
    public function getFullSorAnalysisData(Sor $sor, RateCard $rateCard, $date, $items = null): array
    {
        // Fetch all items for the SOR that are measurable (item_type = 3) if not provided
        // Eager load relationships to optimize performance
        $allItems = $items ?? Item::where('sor_id', $sor->id)
            ->where('item_type', 3)
            ->with([
                'skeletons.resource.group',
                'skeletons.unit',
                'subitems.subItem',
                'subitems.unit',
                'overheads.overhead'
            ])
            ->get();

        $allItemsOrdered = [];
        $processedIds = [];

        foreach ($allItems as $item) {
            $this->buildAnalysisOrder1($item, $allItemsOrdered, $processedIds);
        }

        // Get a unique list of all resources used across all items
        $allResources = $this->getUniqueResourcesForItemsWithRates($allItemsOrdered, $rateCard, $date);

        // Calculate the detailed analysis for each item needed for the view
        $itemAnalyses = [];
        foreach ($allItemsOrdered as $item) {
            $itemAnalyses[] = $this->getDetailedAnalysisForExport($item, $rateCard, $date);
        }

        return [
            'sor' => $sor,
            'rateCard' => $rateCard,
            'date' => $date,
            'ordered_items_analysis' => $itemAnalyses,
            'unique_resources' => $allResources,
        ];
    }

    /**
     * Recursively builds a bottom-up (post-order) list of items.
     */
    private function buildAnalysisOrder1(Item $item, array &$list, array &$processedIds)
    {
        // \Log::info("Processing item: {$item->id}");
        if (in_array($item->id, $processedIds)) {
            return;
        }

        // 1. Recurse through sub-items (dependencies)
        // Use eager loaded subitems if available
        $subItems = $item->subitems;

        foreach ($subItems as $subItemRelation) {
            $childItem = $subItemRelation->subItem;
            if ($childItem) {
                $this->buildAnalysisOrder($childItem, $list, $processedIds);
            }
        }

        // 2. Add the parent after all its children have been added
        if (!in_array($item->id, $processedIds)) {
            $list[] = $item;
            $processedIds[] = $item->id;
            // \Log::info("Added item: {$item->id}");
        }
    }



    private function getDetailedAnalysisForExport(Item $item, RateCard $rateCard, $date)
    {
        // Optimized calculation using eager loaded data

        // 1. Resources
        // Use the collection directly. Sort by sort_order manually if needed, but usually DB returns them in order if not specified?
        // Actually, we should ensure they are sorted. Collection sortBy is fast.
        $resources = $item->skeletons->sortBy('sort_order');

        $resourceData = [];
        $totalLabor = 0;
        $totalMaterial = 0;
        $totalMachine = 0;
        $totalCartage = 0;
        $totalMiscellaneous = 0;

        foreach ($resources as $res) {
            $rateDetails = $this->rateAnalysisService->getResourceRateDetails($res->resource, $rateCard, $date);
            $rate = $rateDetails['total_rate'];
            $unit_id = $rateDetails['unit_id'];

            $rateUnit = \App\Models\Unit::find($unit_id);
            $qtyUnit = $res->unit;

            $conversionFactor = $this->unitService->getConversionFactor($qtyUnit, $rateUnit);
            $amount = $res->quantity * $conversionFactor * $rate;

            $resource_group_id = $res->resource->resource_group_id;

            switch ($resource_group_id) {
                case 1: $totalLabor += $amount; break;
                case 2: $totalMachine += $amount; break;
                case 3: $totalMaterial += $amount; break;
                case 4: $totalCartage += $amount; break;
                default: $totalMiscellaneous += $amount; break;
            }

            $resourceData[] = [
                'id' => $res->id,
                'resource_id' => $res->resource_id,
                'secondary_code' => $res->resource->secondary_code,
                'name' => $res->resource->name,
                'quantity' => $res->quantity,
                'unit' => $res->unit ? $res->unit->name : '',
                'rate' => $rate,
                'amount' => $amount,
            ];
        }

        // 2. Sub-items
        $subitems = $item->subitems->sortBy('sort_order');
        $subitemData = [];
        $totalSubitems = 0;

        foreach ($subitems as $sub) {
            // We need the rate of the sub-item.
            // Since we are building bottom-up, we *could* use the already calculated rate from $itemAnalyses if we passed it.
            // But for now, let's stick to fetching from ItemRate table as per original logic,
            // assuming the rates are already in DB or we don't need to recalculate recursively here.
            // Wait, if we are exporting, we want the *current* calculation.
            // But `ItemRate` stores the *saved* rate.
            // If the user hasn't saved the rate, this might be stale.
            // However, the requirement is to export the analysis.
            // If we want dynamic linking in Excel, we just need the structure.
            // The values in Excel will be recalculated by Excel formulas!
            // So the exact rate value here is just for the "static" view if formulas fail.

            // Let's use ItemRate for now to be consistent with existing service.
            // Optimizing: We can eager load ItemRates? No, complex query.
            // Just query it. It's indexed.
            $subRateEntry = \App\Models\ItemRate::where('item_code', $sub->sub_item_code)
                ->where('rate_card_id', $rateCard->id)
                ->where('valid_from', '<=', $date)
                ->orderBy('valid_from', 'desc')
                ->first();

            $rate = $subRateEntry ? $subRateEntry->rate : 0;
            $amount = $sub->quantity * $rate;
            $totalSubitems += $amount;

            $subitemData[] = [
                'sub_item_code' => $sub->sub_item_code,
                'name' => $sub->subItem->description ?? 'Unknown Item',
                'quantity' => $sub->quantity,
                'unit' => $sub->unit ? $sub->unit->name : '',
                'rate' => $rate,
                'amount' => $amount,
            ];
        }

        // 3. Overheads
        $overHeadRules = $item->overheads->sortBy('sort_order');
        $overheadData = [];
        $totalOverheads = 0;
        $runningTotal = array_sum(array_column($resourceData, 'amount')) + $totalSubitems;
        $costMapOfResources = collect($resourceData)->pluck('amount', 'resource_id');

        foreach ($overHeadRules as $rule) {
            $calculationResult = $this->overheadService->calculateOverheadAmount($rule, [
                'totalLabor' => $totalLabor,
                'totalMachine' => $totalMachine,
                'totalMaterial' => $totalMaterial,
                'totalCartage' => $totalCartage,
                'subItemsWithOh' => $totalSubitems,
                'resourceCosts' => $costMapOfResources ?? [],
                'runningTotal' => $runningTotal,
                'totalOverhead' => $totalOverheads
            ]);

            $overheadAmount = $calculationResult['amount'];
            $baseAmount = $calculationResult['base'];

            if ($rule->allow_further_overhead) {
                $totalOverheads += $overheadAmount;
            }

            $overheadData[] = [
                'description' => $this->overheadService->formatOverheadDescription($rule, $baseAmount),
                'parameter' => round($rule->parameter * 100, 2),
                'amount' => $overheadAmount,
            ];
        }

        $totalOverheads = array_sum(array_column($overheadData, 'amount'));
        $grandTotal = array_sum(array_column($resourceData, 'amount')) + $totalSubitems + $totalOverheads;
        $turnout = $item->turnout_quantity > 0 ? $item->turnout_quantity : 1;
        $finalRate = $grandTotal / $turnout;

        return [
            'item' => $item,
            'name' => $item->description,
            'item_number' => $item->item_number,
            'item_code' => $item->item_code,
            'unit' => $item->unit ? $item->unit->name : '',
            'resources' => $resourceData,
            'sub_items' => $subitemData,
            'overheads' => $overheadData,
            'totals' => [
                'resource_cost' => array_sum(array_column($resourceData, 'amount')),
                'subitem_cost' => $totalSubitems,
                'overhead_cost' => $totalOverheads,
                'grand_total' => $grandTotal,
                'turnout' => $turnout,
                'final_rate' => $finalRate,
            ],
        ];
    }
}
