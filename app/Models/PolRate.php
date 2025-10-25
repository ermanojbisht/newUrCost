<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolRate extends Model
{
    use HasFactory;

    protected $table = 'pol_rates';

    protected $fillable = [
        'rate_date',
        'diesel_rate',
        'mobile_oil_rate',
        'laborer_charges',
        'hiring_charges',
        'overhead_charges',
        'mule_rate',
        'valid_from',
        'valid_to',
        'is_locked',
        'published_at',
    ];

    protected $casts = [
        'rate_date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_locked' => 'boolean',
        'published_at' => 'datetime',
    ];
}