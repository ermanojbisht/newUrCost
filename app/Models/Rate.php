<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $table = 'rates';

    protected $fillable = [
        'resource_id',
        'rate_card_id',
        'applicable_date',
        'unit_id',
        'rate',
        'valid_from',
        'valid_to',
        'remarks',
        'is_locked',
        'published_at',
        'tax',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'applicable_date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'published_at' => 'datetime',
        //'is_locked' => 'boolean',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function rateCard()
    {
        return $this->belongsTo(RateCard::class, 'rate_card_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
