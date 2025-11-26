<?php

namespace App\Services;

use App\Models\Item;
use App\Models\LaborIndex;
use App\Models\LeadDistance;
use App\Models\MachineIndex;
use App\Models\ManMuleCartRule;
use App\Models\PolRate;
use App\Models\PolSkeleton;
use App\Models\Rate;
use App\Models\RateCard;
use App\Models\Resource;
use App\Models\TruckSpeed;
use App\Services\OverheadService;
use App\Services\UnitService;
use Log;

class RateAnalysisService
{
    protected $overheadService;
    protected $unitService;

    public function __construct(OverheadService $overheadService, UnitService $unitService)
    {
        $this->overheadService = $overheadService;
        $this->unitService = $unitService;
    }
    /**
     * Get the rate for a given resource and rate card.
     * getResourceRate => getResourceRateWithUnit
     *MKB USED
     * @param Resource $resource
     * @param RateCard $ratecard
     * @param string $date
     * @return float
     */
    public function getResourceRateWithUnit(Resource $resource, RateCard $ratecard, $date): array
    {
        $baseRateWithUnit = $this->getBaseRateWithUnit($resource, $ratecard, $date);
        $baseRate=$baseRateWithUnit['rate'];
        $unit_id=$baseRateWithUnit['unit_id'];

        // Material Resources
        // it is assumend that unit_id of base rate and  lead distance calculation like capacity all are same
        if ($resource->resource_group_id == 3) {
            $leadDetails = $this->calculateLeadCost($resource, $ratecard, $date);
            return ['total_rate'=> $baseRate + $leadDetails['totalLeadCost'],'unit_id'=>$unit_id];
        }

        // Labor or Machine Resources
        if (in_array($resource->resource_group_id, [1, 2])) {
            list($indexCost,$percentIndex) = $this->calculateIndexCost($resource, $ratecard, $baseRate);
            return ['total_rate'=>$baseRate + $indexCost,'unit_id'=>$unit_id];
        }

        return ['total_rate'=>$baseRate,'unit_id'=>$unit_id];
    }

    /**
     * Get detailed rate breakdown for a resource.
     * MKB USED
     * @param Resource $resource
     * @param RateCard $ratecard
     * @param string $date
     * @return array
     */
    public function getResourceRateDetails(Resource $resource, RateCard $ratecard, $date): array
    {
        $baseRateWithUnit = $this->getBaseRateWithUnit($resource, $ratecard, $date);
        $details = [
            'base_rate' => $baseRateWithUnit['rate'],
            'unit_id' => $baseRateWithUnit['unit_id'],
            'lead_cost' => 0.0,
            'index_cost' => 0.0,
            'total_rate' => $baseRateWithUnit['rate'],
            'components' => []
        ];

        // Determine source for description
        $rateEntry = Rate::where('resource_id', $resource->id)
            ->where('rate_card_id', $ratecard->id)
            ->where('valid_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
            })
            ->first();

        $sourceDesc = $rateEntry ? "Rate Card: {$ratecard->name}" : "Fallback to Basic Rate Card";

        $details['components'][] = [
            'name' => 'Base Rate',
            'amount' => $baseRateWithUnit['rate'],
            'description' => $sourceDesc
        ];

        // Material Resources
        if ($resource->resource_group_id == 3) {
            $leadDetails = $this->calculateLeadCost($resource, $ratecard, $date);
            $totalLeadCost = $leadDetails['totalLeadCost'];

            if ($totalLeadCost != 0) {
                $details['lead_cost'] = $totalLeadCost;
                $details['total_rate'] += $totalLeadCost;

                // Add breakdown components
                if (!empty($leadDetails['mechLeadCost']) && $leadDetails['mechLeadCost'] > 0) {
                    $details['components'][] = [
                        'name' => 'Mechanical Lead',
                        'amount' => $leadDetails['mechLeadCost'],
                        'description' => 'Distance: ' . ($leadDetails['mechDistance'] ?? 0) . ' km'
                    ];
                }
                if (!empty($leadDetails['manualLeadCost']) && $leadDetails['manualLeadCost'] > 0) {
                    $details['components'][] = [
                        'name' => 'Manual Lead',
                        'amount' => $leadDetails['manualLeadCost'],
                        'description' => 'Distance: ' . ($leadDetails['manualDistance'] ?? 0) . ' km'
                    ];
                }
                if (!empty($leadDetails['muleLeadCost']) && $leadDetails['muleLeadCost'] > 0) {
                    $details['components'][] = [
                        'name' => 'Mule Lead',
                        'amount' => $leadDetails['muleLeadCost'],
                        'description' => 'Distance: ' . ($leadDetails['muleDistance'] ?? 0) . ' km'
                    ];
                }

                // Fallback if individual costs are missing but total exists (shouldn't happen with current logic but safe to have)
                if (empty($leadDetails['mechLeadCost']) && empty($leadDetails['manualLeadCost']) && empty($leadDetails['muleLeadCost'])) {
                     $details['components'][] = [
                        'name' => 'Lead Cost',
                        'amount' => $totalLeadCost,
                        'description' => 'Total Lead Cost'
                    ];
                }
            }
        }

        // Labor or Machine Resources
        if (in_array($resource->resource_group_id, [1, 2])) {
            list($indexCost,$percentIndex) = $this->calculateIndexCost($resource, $ratecard, $baseRateWithUnit['rate']);
            if ($indexCost != 0) {
                $details['index_cost'] = $indexCost;
                $details['total_rate'] += $indexCost;
                $details['components'][] = [
                    'name' => 'Index Cost',
                    'amount' => $indexCost,
                    'description' => 'Cost adjustment based on index '. round($percentIndex * 100,2) .' %'
                ];
            }
        }

        return $details;
    }

    /**
     * Get the base rate for a resource, with fallback to the default rate card.
     */
    public function getBaseRateWithUnit(Resource $resource, RateCard $ratecard, $date)
    {
        $fields=['resource_id','rate_card_id','valid_from','valid_to','unit_id','rate'];
        $rate = Rate::select($fields)->where('resource_id', $resource->id)
            ->where('rate_card_id', $ratecard->id)
            ->where('valid_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
            })
            ->first();

        if ($rate) {
            return [ 'rate'=>$rate->rate, 'unit_id'=>$rate->unit_id ] ;
        }

        // Fallback to default rate card (id=1)
        $rate = Rate::where('resource_id', $resource->id)
            ->where('rate_card_id', 1)
            ->where('valid_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
            })
            ->first();

       if ($rate) {
            return [ 'rate'=>$rate->rate, 'unit_id'=>$rate->unit_id ] ;
        }

        return [ 'rate'=>0, 'unit_id'=>$resource->unit_id ] ;
    }

    /**
     * Calculate the lead cost for a material resource.
     */
    public function calculateLeadCost(Resource $resource, RateCard $ratecard ,$date): array
    {
        $leadDetail = ['totalLeadCost'=>0,'mechLeadCost'=>0,'muleLeadCost'=>0,'manualLeadCost'=>0];
        $leadDistances = LeadDistance::where('resource_id', $resource->id)
            ->where('rate_card_id', $ratecard->id)
            ->where('is_canceled', 0)
            ->where('valid_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
            })
            ->get();

        if (!$leadDistances) {
            // Fallback to default rate card (id=1)
            $leadDistances = LeadDistance::where('resource_id', $resource->id)
                ->where('rate_card_id', 1)
                ->where('is_canceled', 0)
                ->where('valid_from', '<=', $date)
                ->where(function ($query) use ($date) {
                    $query->where('valid_to', '>=', $date)
                          ->orWhereNull('valid_to');
                })
                ->get();
        }
        //resource capcity of particular resource from its capcity group defined in resource table
        $resourceCapacityRule=$resource->resourceCapacityRule;
        if ($leadDistances->isEmpty() || !$resourceCapacityRule) {
            return  $leadDetail;
        }



        $totalLeadCost = 0.0;

        foreach ($leadDistances as $lead) {
            switch ($lead->type) {
                case 1: // Mechanical
                    $tripcost = $this->mechanicalCartageDetail( $lead->distance, $date )['costPerTrip'];
                    $mechLeadCost = round( $tripcost / $resourceCapacityRule->net_mechanical_capacity, 2 );
                    $totalLeadCost += $mechLeadCost;
                    $leadDetail[ 'mechLeadCost' ] = $mechLeadCost;
                    $leadDetail[ 'mechDistance' ] = $lead->distance;
                    $leadDetail[ 'mechleadid' ] = $lead->id; //code by nand 6-4-2017 to be deleted in future.
                    break;
                case 2: // Manual
                    //"Lead Type 2";//for mannual lead
                    $mode = $resource->volume_or_weight; //for type a and type B selection by voloume 1 and weight 2
                    $netManualCapacity = $resourceCapacityRule->net_manual_capacity;
                    $polRate=$this->polRate($date);
                    $laborerCharges = $polRate?$polRate->laborer_charges:0;
                    $factor = $this->manMuleCartageFactor( $lead->distance, $mode );
                    $manualLeadCost = round( $factor * $laborerCharges / $netManualCapacity, 2 );
                    //log_message('error','manual:factor= '.$factor.', mazdoorcharges='.$laborerCharges.', ManNetCapacity='.$netManualCapacity.', amt='.$manualLeadCost);
                    $totalLeadCost += $manualLeadCost;
                    $leadDetail[ 'manualLeadCost' ] = $manualLeadCost;
                    $leadDetail[ 'manualDistance' ] = $lead->distance;
                    $leadDetail[ 'manleadid' ] = $lead->id; //code by nand 6-4-2017
                    break;  //***8112 made the part GROUP 10 instead of 0
                case 3: // Mule
                    // Placeholder for manMulecartrule logic
                    $mode = 3;
                    $factor = $this->manMuleCartageFactor( $lead->distance, $mode );
                    $polRate=$this->polRate($date);
                    $MuleRatefromPOL = $polRate?$polRate->mule_rate:0;
                    $MuleFactor = $resourceCapacityRule->mule_factor;
                    $muleLeadCost = round( $factor * $MuleRatefromPOL * $MuleFactor, 2 );
                    //log_message('error','Mule:factor= '.$factor.', MuleRatefromPOL='.$MuleRatefromPOL.', MuleFactor='.$MuleFactor.' , amt='.$muleLeadCost);
                    $totalLeadCost += $muleLeadCost;
                    $leadDetail[ 'muleLeadCost' ] = $muleLeadCost;
                    $leadDetail[ 'muleDistance' ] = $lead->distance;
                    $leadDetail[ 'muleleadid' ] = $lead->id; //code by nand 6-4-2017
                    break;
            }
        }

        $leadDetail['totalLeadCost']=$totalLeadCost;
        return $leadDetail;
    }

    /**
     * Calculate the index cost for a labor or machine resource.
     */
    public function calculateIndexCost(Resource $resource, RateCard $ratecard, float $baseRate)
    {
        $indexModel = $resource->resource_group_id == 1 ? new LaborIndex() : new MachineIndex();

        //Log::info("indexModel = ".print_r($indexModel,true));
        //Log::info("this = ".print_r(['resource_id'=>$resource->id,'rate_card_id'=>$ratecard->id],true));

        // 1. Check for specific resource and rate card
        $index = $indexModel->where('resource_id', $resource->id)
            ->where('rate_card_id', $ratecard->id)
            ->first();

        // 2. Fallback to general rule for the rate card
        if (!$index) {
            $index = $indexModel->where('resource_id', 1) // General rule
                ->where('rate_card_id', $ratecard->id)
                ->first();
        }

        // 3. Fallback to specific resource in default rate card
        if (!$index) {
            $index = $indexModel->where('resource_id', $resource->id)
                ->where('rate_card_id', 1) // Default rate card
                ->first();
        }

        // 4. Fallback to general rule in default rate card
        if (!$index) {
            $index = $indexModel->where('resource_id', 1)
                ->where('rate_card_id', 1)
                ->first();
        }

        $percentIndex = $index ? $index->index_value : 0.0;

        return [(round($baseRate * $percentIndex,2)),$percentIndex];
    }


    public function mechanicalCartageDetail($km, $date)
    {
        // --- 1. Initialize response array with defaults ---
        $result = [
            'km'             => $km,
            'trips'          => 0,
            'totalKmCovered' => 0,

            'dieselRate'     => 0,
            'oilRate'        => 0,
            'laborCharges'   => 0,
            'hiringCharges'  => 0,

            'dieselMileage'  => 0,
            'oilMileage'     => 0,
            'laborCount'     => 0,

            'dieselCost'     => 0,
            'oilCost'        => 0,
            'laborCost'      => 0,
            'totalCost'      => 0,
            'costPerTrip'    => 0,
        ];

        // --- 2. Fetch truck speed ---
        $truckSpeed = TruckSpeed::find($km);
        $averageSpeed = $truckSpeed ? $truckSpeed->average_speed : 31;

        // --- 3. Trip calculations ---
        $trips = 8 / ((2 * $km / $averageSpeed) + 1);
        $totalKmCovered = (2 * $trips * $km) + 6;

        // Put in result
        $result['trips'] = $trips;
        $result['totalKmCovered'] = $totalKmCovered;

        // --- 4. POL rate ---
        $polRate = $this->polRate($date);

        if (!$polRate) {
            return $result; // return initialized array
        }

        $result['dieselRate']    = $polRate->diesel_rate;
        $result['oilRate']       = $polRate->mobile_oil_rate;
        $result['laborCharges']  = $polRate->laborer_charges;
        $result['hiringCharges'] = $polRate->hiring_charges;

        // --- 5. Skeleton POL ---
        $polSkeleton = PolSkeleton::where('valid_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
            })
            ->orderBy('valid_from')
            ->first();

        if (!$polSkeleton) {
            return $result; // return initialized array
        }

        $result['dieselMileage'] = $polSkeleton->diesel_mileage;
        $result['oilMileage']    = $polSkeleton->mobile_oil_mileage;
        $result['laborCount']    = $polSkeleton->number_of_laborers;

        // --- 6. Cost calculations ---
        $dieselCost = ($totalKmCovered * $result['dieselRate']) / $result['dieselMileage'];
        $oilCost    = ($totalKmCovered * $result['oilRate']) / $result['oilMileage'];
        $laborCost  = $result['laborCharges'] * $result['laborCount'];

        $totalCost = $dieselCost + $oilCost + $laborCost + $result['hiringCharges'];
        $costPerTrip = $trips ? ($totalCost / $trips) : 0;

        // Place final values
        $result['dieselCost']  = $dieselCost;
        $result['oilCost']     = $oilCost;
        $result['laborCost']   = $laborCost;
        $result['totalCost']   = $totalCost;
        $result['costPerTrip'] = $costPerTrip;

        return $result;
    }


    public function polRate($date)
    {
        return $polRate = PolRate::where('valid_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
            })
            ->orderBy('valid_from')
            ->first();
    }

    /**
     * @param $distance
     * @param $mode
     * @return int
     */
    public function manMuleCartageFactor( $distance, $mode )
    {
        switch ( $mode )
        {
            case 1:
                //by man vol
                $distance = intval( $distance * 20 ) / 20;
                break;
            case 2:
                //by man weight
                $distance = intval( $distance * 20 ) / 20;
                break;
            case 3:
                //by mule
                if ( $distance > 0 && $distance < 0.5 )
                {
                    $distance = 0.5;
                }
                $distance = intval( $distance * 2 ) / 2;
                break;
            default:
                return 0;
                break;
        }

        $manMuleCartRule=ManMuleCartRule::where('calculation_method',$mode)->where('distance',$distance)->first();
        return $manMuleCartRule?$manMuleCartRule->factor:0;
    }


    /**
     * Get a flattened and aggregated list of all resources and overheads for an item.
     *
     * @param Item $item
     * @param RateCard $rateCard
     * @param string $date
     * @return array
     */
    public function getFlatResourceList(Item $item, RateCard $rateCard, $date): array
    {
        $resourceList = [];
        $overheadList = [];
        $t = $item->turnout_quantity > 0 ? 1 / $item->turnout_quantity : 1;

        $this->buildConsumptionData($item, $t, $rateCard, $date, $resourceList, $overheadList);

        // Aggregate Resources
        $aggregatedResources = [];
        foreach ($resourceList as $resource) {
            //Log::info("resource = ".print_r($resource,true));
            $id = $resource['resource_id'];
            if (!isset($aggregatedResources[$id])) {
                //Log::info("in first resource ");
                $aggregatedResources[$id] = $resource;
                // Calculate amount for the initial entry
                $aggregatedResources[$id]['quantity']=$resource['quantity'];
                $getResourceRateWithUnit = $this->getResourceRateWithUnit($resource['resource_object'], $rateCard, $date);
                $aggregatedResources[$id]['rate'] = $getResourceRateWithUnit['total_rate'];
                $aggregatedList[$id]['runit'] = $getResourceRateWithUnit['unit_id'];
                $aggregatedResources[$id]['amount'] = $getResourceRateWithUnit['total_rate'] * $resource['quantity']* $this->unitService->getConversionFactor($resource['qtyUnit'] , $aggregatedList[$id]['runit']);
            } else {
                //Log::info("in another time resource ");

                $aggregatedResources[$id]['quantity'] += $resource['quantity'];
                // Recalculate amount with new total quantity
                $getResourceRateWithUnit = $this->getResourceRateWithUnit($resource['resource_object'], $rateCard, $date);
                $aggregatedList[$id]['rate'] = $getResourceRateWithUnit['total_rate'];
                $aggregatedList[$id]['runit'] = $getResourceRateWithUnit['unit_id'];
                $aggregatedResources[$id]['amount'] = $getResourceRateWithUnit['total_rate'] * $aggregatedResources[$id]['quantity']*$this->unitService->getConversionFactor($resource['qtyUnit'] , $aggregatedList[$id]['runit']);
            }
        }


        // Aggregate Overheads
        $aggregatedOverheads = [];
        foreach ($overheadList as $overhead) {
            $id = $overhead['overhead_id']; // Group by Overhead ID (Master ID)
            if (!isset($aggregatedOverheads[$id])) {
                $aggregatedOverheads[$id] = $overhead;
            } else {
                $aggregatedOverheads[$id]['amount'] += $overhead['amount'];
            }
        }
        //Log::info("resources = ".print_r(array_values($aggregatedResources),true));
        //Log::info("aggregatedOverheads = ".print_r(array_values($aggregatedOverheads),true));

        return [
            'resources' => array_values($aggregatedResources),
            'overheads' => array_values($aggregatedOverheads)
        ];
    }

    /**
     * Recursive helper to build the flat resource and overhead list.
     * Returns the total cost of the item (per unit of item).
     *
     * @param Item $item
     * @param float $factor
     * @param RateCard $rateCard
     * @param string $date
     * @param array $resourceList
     * @param array $overheadList
     * @return float
     */
    private function buildConsumptionData(Item $item, float $factor, RateCard $rateCard, $date, array &$resourceList, array &$overheadList): float
    {
        $localCosts = [
            'totalLabor' => 0,
            'totalMachine' => 0,
            'totalMaterial' => 0,
            'totalCartage' => 0,
            'totalMisc' => 0,
            'subItemsWithOh' => 0,
            'resourceCosts' => [],
            'runningTotal' => 0,
            'totalOverhead' => 0
        ];

        // 1. Direct Resources
        foreach ($item->skeletons as $skeleton) {
            $getResourceRateWithUnit = $this->getResourceRateWithUnit($skeleton->resource, $rateCard, $date);
            $rate = $getResourceRateWithUnit['total_rate'];
            $rateUnit = $getResourceRateWithUnit['unit_id'];
            $amount = $skeleton->quantity * $rate*  $this->unitService->getConversionFactor($skeleton->unit_id , $rateUnit);

            // Add to Global List (Scaled)
            $resourceList[] = [
                'resource_id' => $skeleton->resource_id,
                'resource_object' => $skeleton->resource,
                'group' => $skeleton->resource->group->name ?? 'Unknown',
                'name' => $skeleton->resource->name,
                'unit' => $skeleton->unit->name ?? '',
                'quantity' => $skeleton->quantity * $factor,
                'qtyUnit' => $skeleton->unit_id,
            ];

            // Add to Local Costs (Unscaled)
            $localCosts['resourceCosts'][$skeleton->resource_id] = $amount;

            $groupId = $skeleton->resource->resource_group_id; // Assuming column name
            // Map group IDs to cost categories
            if ($groupId == 1) $localCosts['totalLabor'] += $amount;
            elseif ($groupId == 2) $localCosts['totalMachine'] += $amount;
            elseif ($groupId == 3) $localCosts['totalMaterial'] += $amount;
            elseif ($groupId == 4) $localCosts['totalCartage'] += $amount;
            elseif ($groupId == 5) $localCosts['totalMisc'] += $amount;

            $localCosts['runningTotal'] += $amount;
        }

        // 2. Sub-items
        foreach ($item->subitems as $subItemRelation) {
            $childItem = $subItemRelation->subItem;
            if ($childItem) {
                $turnout = $childItem->turnout_quantity > 0 ? $childItem->turnout_quantity : 1;
                $subItemQty = $subItemRelation->quantity;

                // New factor for global list = Current Factor * (SubItem Qty / Turnout)
                $newFactor = $factor * ($subItemQty / $turnout);

                // Recurse to get cost of 1 unit of subitem
                $subUnitCost = $this->buildConsumptionData($childItem, $newFactor, $rateCard, $date, $resourceList, $overheadList);

                // Calculate cost contribution to this item (Batch)
                // Cost = SubUnitCost * SubItemQty
                // Wait, SubItemQty is for the batch (turnout) of this item.
                $subCost = $subUnitCost * $subItemQty;

                if ($subItemRelation->is_oh_applicable) {
                    $localCosts['subItemsWithOh'] += $subCost;
                }
                $localCosts['runningTotal'] += $subCost;
            }
        }

        // 3. Overheads
        // Need to fetch overheads for this item
        // Assuming $item->overheads() relationship exists and is loaded or we load it
        $overheads = $item->overheads()->orderBy('sort_order')->get(); // Or use existing relation if eager loaded

        foreach ($overheads as $rule) {
            $res = $this->overheadService->calculateOverheadAmount($rule, $localCosts);
            $amount = $res['amount'];

            // Add to Global List (Scaled)
            // Amount is for the Batch (Turnout).
            // We need Amount per Unit of Item -> Amount / Turnout
            // Then Scale by Factor (Units of Item needed for Root)
            // GlobalAmount = (Amount / Turnout) * Factor
            // Or Amount * (Factor / Turnout)

            $turnout = $item->turnout_quantity > 0 ? $item->turnout_quantity : 1;
            $scaledAmount = $amount * ($factor / $turnout); // Wait, $factor is 1/Turnout for root.
            // Let's trace:
            // Root (T=1). Factor = 1/1 = 1.
            // OH Amount = 100. Scaled = 100 * (1/1) = 100. Correct.

            // Sub (T=10). Needed 5.
            // Factor = 1 * (5/10) = 0.5.
            // OH Amount (for batch of 10) = 50.
            // Per Unit = 5.
            // Needed 5 units -> Total OH = 25.
            // Formula: 50 * (0.5 / 10) = 50 * 0.05 = 2.5. INCORRECT.

            // Let's re-evaluate Factor.
            // Root (T=1). Factor = 1/1 = 1.
            // Sub (T=10). Needed 5 for Root Batch (1).
            // Factor passed to Sub = 1 * (5/10) = 0.5.
            // This Factor (0.5) means "0.5 units of Sub needed for 1 unit of Root".
            // Wait, my initial Factor logic for Resources was:
            // Qty * Factor.
            // Resource Qty is for Batch (10).
            // So Resource Qty per Unit = Qty / 10.
            // Total Resource needed = (Qty/10) * 0.5? No.
            // If I need 0.5 units of Sub.
            // And Sub (Batch 10) has 50 Res.
            // Res per unit = 5.
            // Total Res = 5 * 0.5 = 2.5.

            // My Resource Logic:
            // $resourceList[] = Qty * Factor.
            // 50 * 0.5 = 25.
            // But 25 is wrong. It should be 2.5.
            // Why?
            // Because Qty (50) is for Batch (10).
            // Factor (0.5) is "Units of Sub needed".
            // So we need (Qty / Turnout) * Factor.
            // My previous logic `quantity * factor` assumed `factor` accounts for turnout?
            // Let's check `buildFlatResourceList` logic again.
            // `newFactor = factor * ($subItemQty / $turnout)`.
            // Initial factor = 1.0 (User changed it to `1/$item->turnout_quantity`).
            // If User changed initial factor to `1/Turnout`, then initial factor is "Per Unit".
            // Let's trace User's change: `$t=1/$item->turnout_quantity; $this->buildFlatResourceList(..., $t, ...);`

            // Root (T=1). Factor = 1.
            // Res Qty (for T=1) = 10.
            // Added: 10 * 1 = 10. Correct.

            // Sub (T=10). Needed 5 for Root Batch (1).
            // newFactor = 1 * (5/10) = 0.5.
            // Res Qty (for T=10) = 50.
            // Added: 50 * 0.5 = 25.
            // Wait.
            // Root needs 5 Sub.
            // Sub (Batch 10) has 50 Res.
            // 1 Sub has 5 Res.
            // 5 Sub has 25 Res.
            // So 25 is CORRECT.

            // So `Qty * Factor` is correct for Resources.

            // Now Overheads.
            // OH Amount (for Batch 10) = 50.
            // Added: 50 * Factor (0.5) = 25.
            // 1 Sub has 5 OH.
            // 5 Sub has 25 OH.
            // So `Amount * Factor` is CORRECT.

            $overheadList[] = [
                'overhead_id' => $rule->overhead_id,
                'name' => $rule->overhead->description ?? 'Unknown',
                'amount' => $amount * $factor,
                'group' => 'Overhead', // For consistency
            ];

            if ($rule->allow_further_overhead) {
                $localCosts['totalOverhead'] += $amount;
            }
        }

        return $localCosts['runningTotal'] + $localCosts['totalOverhead'];
    }















    /**
     * Get a flattened and aggregated list of all resources for an item.
     * getFlatResourceListWithoutOh may be removed
     * @param Item $item
     * @param RateCard $rateCard
     * @param string $date
     * @return array
     */
    public function getFlatResourceListWithoutOh(Item $item, RateCard $rateCard, $date): array
    {
        $resourceList = [];
        $t=1/$item->turnout_quantity;
        $this->buildFlatResourceList($item, $t, $rateCard, $date, $resourceList);

        //Log::info("resourceList = ".print_r($resourceList,true));

        // Aggregate the results
        $aggregatedList = [];
        foreach ($resourceList as $resource) {
            $id = $resource['resource_id'];
            if (!isset($aggregatedList[$id])) {
                $aggregatedList[$id] = $resource;
                // Calculate amount for the initial entry
                $aggregatedList[$id]['quantity'] = $resource['quantity'];
                $getResourceRateWithUnit = $this->getResourceRateWithUnit($resource['resource_object'], $rateCard, $date);
                $aggregatedList[$id]['rate'] = $getResourceRateWithUnit['total_rate'];
                $aggregatedList[$id]['runit'] = $getResourceRateWithUnit['unit_id'];
                $aggregatedList[$id]['amount'] = $getResourceRateWithUnit['total_rate'] * $resource['quantity']* $this->unitService->getConversionFactor($resource['qtyUnit'] , $getResourceRateWithUnit['unit_id']);
            } else {
                $aggregatedList[$id]['quantity'] += $resource['quantity'];
                // Recalculate amount with new total quantity
                $getResourceRateWithUnit = $this->getResourceRateWithUnit($resource['resource_object'], $rateCard, $date);
                $aggregatedList[$id]['rate'] = $getResourceRateWithUnit['total_rate'];
                $aggregatedList[$id]['runit'] = $getResourceRateWithUnit['unit_id'];
                $aggregatedList[$id]['amount'] = $getResourceRateWithUnit['total_rate'] * $aggregatedList[$id]['quantity']*$this->unitService->getConversionFactor($resource['qtyUnit'] , $getResourceRateWithUnit['unit_id']);
            }
        }

        return array_values($aggregatedList);
    }

    /**
     * Recursive helper to build the flat resource list.
     *
     * @param Item $item
     * @param float $factor
     * @param RateCard $rateCard
     * @param string $date
     * @param array $resourceList
     */
    private function buildFlatResourceList(Item $item, float $factor, RateCard $rateCard, $date, array &$resourceList)
    {
        // 1. Add direct resources
        foreach ($item->skeletons as $skeleton) {
            $resourceList[] = [
                'resource_id' => $skeleton->resource_id,
                'resource_object' => $skeleton->resource,
                'group' => $skeleton->resource->group->name ?? 'Unknown',
                'name' => $skeleton->resource->name,
                'unit' => $skeleton->unit->name ?? '',
                'qtyUnit' => $skeleton->unit_id ?? '',
                'quantity' => $skeleton->quantity * $factor,
            ];
        }

        Log::info("resourceList = ".print_r($resourceList,true));

        // 2. Recursively process sub-items
        foreach ($item->subitems as $subItemRelation) {
            $childItem = $subItemRelation->subItem;
            if ($childItem) {
                $turnout = $childItem->turnout_quantity > 0 ? $childItem->turnout_quantity : 1;
                // The quantity of subitem needed per unit of parent item
                $subItemQty = $subItemRelation->quantity;

                // New factor = Current Factor * (SubItem Qty / Turnout)
                $newFactor = $factor * ($subItemQty / $turnout);

                $this->buildFlatResourceList($childItem, $newFactor, $rateCard, $date, $resourceList);
            }
        }
    }
}
