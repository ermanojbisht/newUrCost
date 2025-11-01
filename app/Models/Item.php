<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

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
        return $this->hasMany(Skeleton::class);
    }

    public function subitems()
    {
        return $this->hasMany(Subitem::class);
    }

    public function oheads()
    {
        return $this->hasMany(Ohead::class);
    }

    public function itemRates()
    {
        return $this->hasMany(ItemRate::class);
    }

    public function getRateFor($ratecard, $date)
    {
        return $this->itemRates()
            ->where('rate_card_id', $ratecard->id)
            ->where('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->first();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
