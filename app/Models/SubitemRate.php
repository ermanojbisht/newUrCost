<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubitemRate extends Model
{
    use HasFactory;

    protected $table = 'subitem_rates';

    protected $fillable = [
        'sub_item_id',
        'rate',
        'labor_cost',
        'material_cost',
        'machine_cost',
        'overhead_cost',
        'rate_card_id',
        'applicable_date',
        'valid_from',
        'valid_to',
        'unit_id',
        'is_locked',
    ];

    protected $casts = [
        'applicable_date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_locked' => 'boolean',
    ];

    public function subItem()
    {
        return $this->belongsTo(Item::class, 'sub_item_id');
    }

    public function rateCard()
    {
        return $this->belongsTo(Ratecard::class, 'rate_card_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}