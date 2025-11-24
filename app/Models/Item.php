<?php

namespace App\Models;

use App\Models\Subitem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Log;

class Item extends Model
{
    use HasFactory, NodeTrait;

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

    public function getRateFor($ratecard, $date)
    {
        $itemToRate = $this->ref_from ? $this->refFrom : $this;

        return $itemToRate->itemRates()
            ->where('rate_card_id', $ratecard->id)
            ->where('valid_to', '<=', $date)
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

}
