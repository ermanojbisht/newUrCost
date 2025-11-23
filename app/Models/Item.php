<?php

namespace App\Models;

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
        return $this->hasMany(ItemRate::class);
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
}
