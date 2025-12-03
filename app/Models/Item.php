<?php

namespace App\Models;

use App\Models\Subitem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Log;
use DB;

class Item extends Model
{
    use NodeTrait;

    protected $table = 'items';

    protected $fillable = [
        'sor_id',
        'parent_id',
        'item_code',
        'name',
        'order_in_parent',
        'specification_code',
        'specification_page_number',
        'item_type',
        'sort_order',
        'item_number',
        'description',
        'short_description',
        'turnout_quantity',
        'assumptions',
        'footnotes',
        'unit_id',
        'is_canceled',
        'nested_list_order',
        'sub_item_level',
        'sub_item_count',
        'old_item_code',
        'dsr_16_id',
        'is_locked',
        'created_by',
        'updated_by',
        'reference_from',
        'lft',
        'rgt',
        'depth',
    ];

    protected $casts = [
        'item_type' => 'integer',
    ];

    public function sor()
    {
        return $this->belongsTo(Sor::class);
    }

    public function parent()
    {
        return $this->belongsTo(Item::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Item::class, 'parent_id');
    }

    public function skeletons()
    {
        return $this->hasMany(Skeleton::class, 'item_code', 'item_code');
    }

    public function subitems()
    {
        return $this->hasMany(Subitem::class, 'item_code', 'item_code');
    }

    public function overheads()
    {
        return $this->hasMany(Ohead::class, 'item_id', 'item_code')->orderBy('sort_order', 'asc');
    }

    public function itemRates()
    {
        return $this->hasMany(ItemRate::class, 'item_code', 'item_code');
    }

    public function getLftName()
    {
        return 'lft';
    }

    public function getRgtName()
    {
        return 'rgt';
    }

    public function getParentIdName()
    {
        return 'parent_id';
    }

    public function getScopedColumns()
    {
        return ['sor_id'];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function technicalSpec()
    {
        return $this->hasOne(ItemTechnicalSpec::class);
    }

    /*public function refFrom()
    {
        return $this->belongsTo(Item::class, 'ref_from');
    }

    public function getEffectiveDescriptionAttribute()
    {
        return $this->ref_from ? $this->refFrom->description : $this->description;
    }*/

    public function getEffectiveUnitIdAttribute()
    {
        return $this->ref_from ? $this->refFrom->unit_id : $this->unit_id;
    }


    public function rateInRateCardOnDate($rateCardId = null, $date = null, $fallback = true)
    {
        return \App\Models\ItemRate::fetchActiveFor(
            $this->item_code,
            $rateCardId,   // must be passed; fallback to 1 handled in fetchActiveFor()
            $date,
            $fallback
        );
    }
    //may be getRateFor replaced with rateInRateCardOnDate


    public function getRateFor($ratecard, $date)
    {
        //mkb checked and found correct
        return $this->itemRates()
                ->where('rate_card_id', $ratecard->id)
                ->where('valid_from', '<=', $date)
                ->where(function($q) use ($date) {
                    $q->where('valid_to', '>=', $date)
                      ->orWhereNull('valid_to');
                })
                ->orderByDesc('valid_to')
                ->first();
    }



    /**
    * Get the total count of all nested sub-items.
    * This method provides an efficient way to get the total count of all
    * nested sub-items by querying the pre-calculated `subitem_dependencies` table.
    **/
    public function getTotalSubItemsCount()
    {
        return SubitemDependency::where('item_code', $this->item_code)->count();
    }

    public function getDynamicSubItemsCount()
    {
        $count = 0;

        // The 'subitems' relationship gets the pivot model records.
        // We loop through them to get to the actual child Item model.
        foreach ($this->subitems as $subitem_pivot) {
            $childItem = $subitem_pivot->subItem; // 'subItem' gets the related Item model

            if ($childItem) {
                // Count the direct child
                $count++;
                // Recursively add the count of its children
                $count += $childItem->getDynamicSubItemsCount();
            }
        }

        return $count;
    }

    /**
     * Trigger the generation of subitem dependencies for this item.
     * This is a wrapper around Subitem::generateSubitemDependency.
     * Already done when subitem added ,edited,deleted through process . it is only if you want to update mannualy
     */
    public function refreshSubitemDependencies()
    {
        Subitem::generateSubitemDependency($this->item_code);
    }



    public function scopeWithDepth($query)
    {
        $subquery = SubitemDependency::selectRaw('sub_item_code, MAX(level) AS depth')
            ->groupBy('sub_item_code');

        return $query->leftJoinSub($subquery, 'deps', 'items.item_code', '=', 'deps.sub_item_code')
                     ->select('items.*', DB::raw('COALESCE(deps.depth, 0) AS depth'));
    }

    public function scopeForSor($query, $sorId)
    {
        return $query->when($sorId, fn($q) => $q->where('items.sor_id', $sorId));
    }

    /**
     * [scopeSubitemsOnly description]
     * @param  [type] $query [description]
     * @param  [type] $flag  [description]
     * @return [type]        [description]
     * must call subitemsOnly() after withDepth()
     */
    public function scopeSubitemsOnly($query, $flag)
    {
        return $query->when($flag, fn($q) => $q->whereNotNull('deps.sub_item_code'));
    }

}
