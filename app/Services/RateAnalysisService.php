<?php

namespace App\Services;

use App\Models\Item;
use App\Models\LaborIndex;
use App\Models\LeadDistance;
use App\Models\MachineIndex;
use App\Models\Rate;
use App\Models\Ratecard;
use App\Models\Resource;

class RateAnalysisService
{
    /**
     * Calculate the rate analysis for a given item and rate card.
     *
     * @param Item $item
     * @param Ratecard $ratecard
     * @return array
     */
    public function calculateRate(Item $item, Ratecard $ratecard): array
    {
        $directResources = $this->getDirectResources($item, $ratecard);
        $subItems = $this->getSubItems($item, $ratecard);

        $resourceCost = collect($directResources)->sum('amount');
        $subItemCost = collect($subItems)->sum('amount');

        $totalDirectCost = $resourceCost + $subItemCost;

        $overheads = $this->getOverheads($item);
        $calculatedOverheads = $this->calculateOverheadCosts($overheads, $directResources, $subItems, $totalDirectCost);
        $totalOverheadCost = collect($calculatedOverheads)->sum('amount');

        $totalCost = $totalDirectCost + $totalOverheadCost;

        $finalRate = ($item->turn_out_quantity > 0) ? $totalCost / $item->turn_out_quantity : 0;

        $analysis = [
            'resources' => $directResources,
            'sub_items' => $subItems,
            'overheads' => $calculatedOverheads,
            'total_direct_cost' => $totalDirectCost,
            'total_overhead_cost' => $totalOverheadCost,
            'total_cost' => $totalCost,
            'rate' => $finalRate,
        ];

        return $analysis;
    }

    private function getOverheads(Item $item): \Illuminate\Database\Eloquent\Collection
    {
        return $item->oheads()->orderBy('sorder')->get();
    }

    private function calculateOverheadCosts( $overheads, array $resources, array $subItems, float $totalDirectCost): array
    {
        $calculated = [];
        $cumulativeOverhead = 0;

        // Placeholder logic - this needs to be a careful translation of the legacy 'oon' switch statement
        foreach ($overheads as $overhead) {
            $amount = 0;
            // Simple placeholder: 5% of total direct cost for all overheads
            $amount = $totalDirectCost * 0.05;

            $calculated[] = [
                'name' => $overhead->ohdesc,
                'amount' => $amount,
            ];
            $cumulativeOverhead += $amount;
        }

        return $calculated;
    }

    private function getSubItems(Item $item, Ratecard $ratecard): array
    {
        $subItemData = [];

        foreach ($item->subitems as $subitem) {
            // Recursive call to calculate the rate for the sub-item
            $subItemAnalysis = $this->calculateRate($subitem->subItem, $ratecard);

            $rate = $subItemAnalysis['rate'];
            $amount = $subitem->quantity * $rate;

            $subItemData[] = [
                'name' => $subitem->subItem->item_no . ' ' . $subitem->subItem->item_short_desc,
                'quantity' => $subitem->quantity,
                'rate' => $rate,
                'amount' => $amount,
            ];
        }

        return $subItemData;
    }

    /**
     * Get the direct resources for a given item.
     *
     * @param Item $item
     * @param Ratecard $ratecard
     * @return array
     */
    private function getDirectResources(Item $item, Ratecard $ratecard): array
    {
        $resources = [];

        foreach ($item->skeletons as $skeleton) {
            $rate = $this->getResourceRate($skeleton->resource, $ratecard);
            $amount = $skeleton->quantity * $rate;

            $resources[] = [
                'name' => $skeleton->resource->name,
                'quantity' => $skeleton->quantity,
                'rate' => $rate,
                'amount' => $amount,
            ];
        }

        return $resources;
    }

    /**
     * Get the rate for a given resource and rate card.
     *
     * @param Resource $resource
     * @param Ratecard $ratecard
     * @return float
     */
    private function getResourceRate(Resource $resource, Ratecard $ratecard): float
    {
        $baseRate = $this->getBaseRate($resource, $ratecard);

        // Material Resources
        if ($resource->resource_group_id == 3) {
            $leadCost = $this->calculateLeadCost($resource, $ratecard);
            return $baseRate + $leadCost;
        }

        // Labor or Machine Resources
        if (in_array($resource->resource_group_id, [1, 2])) {
            $indexCost = $this->calculateIndexCost($resource, $ratecard, $baseRate);
            return $baseRate + $indexCost;
        }

        return $baseRate;
    }

    /**
     * Get the base rate for a resource, with fallback to the default rate card.
     */
    private function getBaseRate(Resource $resource, Ratecard $ratecard): float
    {
        $rate = Rate::where('resource_id', $resource->id)
            ->where('ratecard_id', $ratecard->id)
            ->latest('created_at') // Ignoring predate/postdate for now
            ->first();

        if ($rate) {
            return $rate->rate;
        }

        // Fallback to default rate card (id=1)
        $rate = Rate::where('resource_id', $resource->id)
            ->where('ratecard_id', 1)
            ->latest('created_at')
            ->first();

        return $rate ? $rate->rate : 0.0;
    }

    /**
     * Calculate the lead cost for a material resource.
     */
    private function calculateLeadCost(Resource $resource, Ratecard $ratecard): float
    {
        $leadDistances = LeadDistance::where('resource_id', $resource->id)
            ->where('ratecard_id', $ratecard->id)
            ->get();

        if ($leadDistances->isEmpty()) {
            return 0.0;
        }

        $totalLeadCost = 0.0;

        foreach ($leadDistances as $lead) {
            switch ($lead->lead_type) {
                case 1: // Mechanical
                    // Placeholder for complex meccartag logic
                    $totalLeadCost += $lead->lead * 0.5; // Simple placeholder: 0.5 per km
                    break;
                case 2: // Manual
                    // Placeholder for manMulecartrule logic
                    $totalLeadCost += $lead->lead * 2; // Simple placeholder: 2 per km
                    break;
                case 3: // Mule
                    // Placeholder for manMulecartrule logic
                    $totalLeadCost += $lead->lead * 5; // Simple placeholder: 5 per km
                    break;
            }
        }

        return $totalLeadCost;
    }

    /**
     * Calculate the index cost for a labor or machine resource.
     */
    private function calculateIndexCost(Resource $resource, Ratecard $ratecard, float $baseRate): float
    {
        $indexModel = $resource->resource_group_id == 1 ? new LaborIndex() : new MachineIndex();

        // 1. Check for specific resource and rate card
        $index = $indexModel->where('resource_id', $resource->id)
            ->where('ratecard_id', $ratecard->id)
            ->first();

        // 2. Fallback to general rule for the rate card
        if (!$index) {
            $index = $indexModel->where('resource_id', 1) // General rule
                ->where('ratecard_id', $ratecard->id)
                ->first();
        }

        // 3. Fallback to specific resource in default rate card
        if (!$index) {
            $index = $indexModel->where('resource_id', $resource->id)
                ->where('ratecard_id', 1) // Default rate card
                ->first();
        }

        // 4. Fallback to general rule in default rate card
        if (!$index) {
            $index = $indexModel->where('resource_id', 1)
                ->where('ratecard_id', 1)
                ->first();
        }

        $percentIndex = $index ? $index->percent_index : 0.0;

        return $baseRate * $percentIndex;
    }
}
