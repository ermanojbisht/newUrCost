<?php

namespace App\\\Models;

use Illuminate\\\Database\\\Eloquent\\\Factories\\\HasFactory;
use Illuminate\\\Database\\\Eloquent\\\Model;

class PolSkeleton extends Model
{
    use HasFactory;

    protected $table = 'pol_skeletons';

    protected $fillable = [
        'date',
        'diesel_mileage',
        'mobile_oil_mileage',
        'number_of_laborers',
        'valid_from',
        'valid_to',
        'is_locked',
    ];

    protected $casts = [
        'date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_locked' => 'boolean',
    ];
}