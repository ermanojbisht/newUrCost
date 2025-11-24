<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRate extends Model
{
    use HasFactory;

    protected $table = 'item_rates';

    protected $primaryKey = ['item_id', 'rate_card_id', 'calculation_date'];
    public $incrementing = false;

    protected $fillable = [
        'item_code',
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

    protected $casts = [
        'calculation_date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function rateCard()
    {
        return $this->belongsTo(RateCard::class, 'rate_card_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }


    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_to')
              ->orWhere('valid_to', '2038-01-19');
        });
    }

    public function isLocked()
    {
        return (bool) $this->is_locked;
    }

    public function closeAt(Carbon $date)
    {
        if ($this->isLocked()) {
            throw new \Exception("Rate is locked. Only valid_to can be changed.");
        }

        $this->valid_to = $date->toDateString();
        $this->save();
    }

    public function forceCloseAt(Carbon $date)
    {
        $this->valid_to = $date->toDateString();
        $this->save();
    }

    public function overlaps(Carbon $date)
    {
        if (is_null($this->valid_from)) return false;

        return $date->gte($this->valid_from) &&
               (is_null($this->valid_to) || $date->lte($this->valid_to));
    }
}
