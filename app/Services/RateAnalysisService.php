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
use App\Models\Ratecard;
use App\Models\Resource;
use App\Models\TruckSpeed;
use Log;

class RateAnalysisService
{
    /**
     * Calculate the rate analysis for a given item and rate card.
     *
     * @param Item $item
     * @param Ratecard|null $ratecard
     * @param string|null $date
     * @return array
     */
    public function calculateRate(Item $item, ?Ratecard $ratecard = null, $date = null): array
    {
        if (!$ratecard) {
            $ratecard = Ratecard::find(1); // Default to Basic Rate Card
        }

        if (!$date) {
            $date = now()->toDateString();
        }

        $directResources = $this->getDirectResources($item, $ratecard, $date);
        $subItems = $this->getSubItems($item, $ratecard, $date);

        $resourceCost = collect($directResources)->sum('amount');
        $subItemCost = collect($subItems)->sum('amount');

        $totalDirectCost = $resourceCost + $subItemCost;

        $overheads = $this->getOverheads($item);
        $calculatedOverheads = $this->calculateOverheadCosts($overheads, $directResources, $subItems, $totalDirectCost);
        $totalOverheadCost = collect($calculatedOverheads)->sum('amount');

        $totalCost = $totalDirectCost + $totalOverheadCost;

        $finalRate = ($item->turn_out_quantity > 0) ? $totalCost / $item->turn_out_quantity : $totalCost;

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

    public function getOverheads(Item $item): \Illuminate\Database\Eloquent\Collection
    {
        return $item->oheads()->orderBy('sorder')->get();
    }

    public function calculateOverheadCosts($overheads, array $resources, array $subItems, float $totalDirectCost): array
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

    public function getSubItems(Item $item, Ratecard $ratecard, $date): array
    {
        $subItemData = [];

        foreach ($item->subitems as $subitem) {
            // Recursive call to calculate the rate for the sub-item
            $subItemAnalysis = $this->calculateRate($subitem->subItem, $ratecard, $date);

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
     * @param string $date
     * @return array
     */
    public function getDirectResources(Item $item, Ratecard $ratecard, $date): array
    {
        $resources = [];

        foreach ($item->skeletons as $skeleton) {
            $rate = $this->getResourceRate($skeleton->resource, $ratecard, $date);
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
     * @param string $date
     * @return float
     */
    public function getResourceRate(Resource $resource, Ratecard $ratecard, $date): float
    {
        $baseRate = $this->getBaseRate($resource, $ratecard, $date);

        // Material Resources
        if ($resource->resource_group_id == 3) {
            $leadDetails = $this->calculateLeadCost($resource, $ratecard, $date);
            return $baseRate + $leadDetails['totalLeadCost'];
        }

        // Labor or Machine Resources
        if (in_array($resource->resource_group_id, [1, 2])) {
            list($indexCost,$percentIndex) = $this->calculateIndexCost($resource, $ratecard, $baseRate);
            return $baseRate + $indexCost;
        }

        return $baseRate;
    }

    /**
     * Get detailed rate breakdown for a resource.
     *
     * @param Resource $resource
     * @param Ratecard $ratecard
     * @param string $date
     * @return array
     */
    public function getResourceRateDetails(Resource $resource, Ratecard $ratecard, $date): array
    {
        $baseRate = $this->getBaseRate($resource, $ratecard, $date);
        $details = [
            'base_rate' => $baseRate,
            'lead_cost' => 0.0,
            'index_cost' => 0.0,
            'total_rate' => $baseRate,
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
            'amount' => $baseRate,
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
            list($indexCost,$percentIndex) = $this->calculateIndexCost($resource, $ratecard, $baseRate);
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
    public function getBaseRate(Resource $resource, Ratecard $ratecard, $date): float
    {
        $rate = Rate::where('resource_id', $resource->id)
            ->where('rate_card_id', $ratecard->id)
            ->where('valid_from', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
            })
            ->first();

        if ($rate) {
            return $rate->rate;
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

        return $rate ? $rate->rate : 0.0;
    }

    /**
     * Calculate the lead cost for a material resource.
     */
    public function calculateLeadCost(Resource $resource, Ratecard $ratecard ,$date): array
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
    public function calculateIndexCost(Resource $resource, Ratecard $ratecard, float $baseRate)
    {
        $indexModel = $resource->resource_group_id == 1 ? new LaborIndex() : new MachineIndex();

        //Log::info("indexModel = ".print_r($indexModel,true));
        Log::info("this = ".print_r(['resource_id'=>$resource->id,'rate_card_id'=>$ratecard->id],true));

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


}
