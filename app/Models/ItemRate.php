<?php

namespace App	Models;

use Illuminate	Database	Eloquent	Factories	HasFactory;
use Illuminate	Database	Eloquent	Model;

class ItemRate	extends	Model
{
    use HasFactory;

    protected $table	= 	'item_rates';

    protected $primaryKey	= 	['item_id', 	'rate_card_id', 	'calculation_date'];
    public $incrementing	= 	false;

    protected $fillable	= 	[
        'item_id',
        'rate',
        'labor_cost',
        'material_cost',
        'machine_cost',
        'overhead_cost',
        'rate_card_id',
        'calculation_date',
        'valid_from',
        'valid_to',
        'unit_id',
    ];

    protected $casts	= 	[
        'calculation_date'	=> 	'date',
        'valid_from'	=> 	'date',
        'valid_to'	=> 	'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function rateCard()
    {
        return $this->belongsTo(Ratecard::class, 	'rate_card_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}