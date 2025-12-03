<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemRate extends Model
{
    use HasFactory;

    protected $table = 'item_rates';

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
        'rate' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'material_cost' => 'decimal:2',
        'machine_cost' => 'decimal:2',
        'overhead_cost' => 'decimal:2',
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

    /**
     * Order rates so the "most relevant/most recent" appears first.
     * Priority:
     *  1. Later valid_from (more recent effective date)
     *  2. Longer valid_to (so indefinite/2038 appears above short-lived ones)
     *  3. created_at desc as tie-breaker
     */
    public function scopeMostRelevantFirst(Builder $query)
    {
        // valid_from NULL should be treated as very old: we sort NULLs last by using ISNULL in DB if needed,
        // but simpler: order by valid_from desc (NULLs will appear last on most DBs)
        return $query->orderByDesc('valid_from')
                     ->orderByDesc('valid_to')    // pushes 2038 or big dates up
                     ->orderByDesc('created_at');
    }

    /**
     * Scope rates effective on a given date (defaults to now).
     * Inclusive: valid_from <= $date AND (valid_to IS NULL OR valid_to >= $date)
     * Treat '2038-01-19' as infinite (still valid).
     */
    public function scopeEffectiveOn(Builder $query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        return $query->where(function ($q) use ($date) {
            // valid_from <= date OR valid_from IS NULL
            $q->where(function ($sub) use ($date) {
                $sub->whereNull('valid_from')
                    ->orWhere('valid_from', '<=', $date->toDateTimeString());
            })
            // AND (valid_to IS NULL OR valid_to >= date OR valid_to = '2038-01-19')
            ->where(function ($sub) use ($date) {
                $sub->whereNull('valid_to')
                    ->orWhere('valid_to', '>=', $date->toDateTimeString())
                    ->orWhere('valid_to', '2038-01-19');
            });
        });
    }

        /**
     * Convenience to get the single active rate with optional fallback.
     *
     * @param  int|\Illuminate\Support\Carbon|string|null  $date
     * @param  bool|callable|null  $fallback  If true -> return latest-ever; if callable -> call it to produce fallback; if false -> return null
     * @return ItemRate|null
     */
    public static function fetchActiveFor($itemCode, $rateCardId = null, $date = null, $fallback = true)
    {
        $rateCardId = $rateCardId ?: 1; // default to BASIC rate card (id=1)
        $date = $date ? Carbon::parse($date) : Carbon::now();

        $q = static::where('item_code', $itemCode)
            ->where('rate_card_id', $rateCardId)
            ->effectiveOn($date)
            ->mostRelevantFirst();

        $rate = $q->first();

        if ($rate) {
            return $rate;
        }

        // Fall back logic…
        if ($fallback === false) {
            return null;
        }

        if (is_callable($fallback)) {
            return $fallback();
        }

        // default fallback: latest-ever rate for this card
        return static::where('item_code', $itemCode)
            ->where('rate_card_id', $rateCardId)
            ->orderByDesc('valid_from')
            ->orderByDesc('created_at')
            ->first();
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
