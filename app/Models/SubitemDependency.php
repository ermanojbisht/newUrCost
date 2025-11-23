<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubitemDependency extends Model
{
    use HasFactory;

    protected $table = 'subitem_dependencies';

    protected $fillable = [
        'item_code',
        'sub_item_code',
        'level',
        'position',
        'quantity',
        'unit_id',
        'parent_turnout_quantity',
        'parent_carries_overhead',
        'parent_overhead_applicability',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'parent_carries_overhead' => 'boolean',
        'parent_overhead_applicability' => 'boolean',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_code');
    }

    public function subItem()
    {
        return $this->belongsTo(Item::class, 'sub_item_code');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
