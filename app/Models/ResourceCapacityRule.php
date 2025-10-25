<?php

namespace App\\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceCapacityRule extends Model
{
    use HasFactory;

    protected $table = 'resource_capacity_rules';

    protected $fillable = [
        'mechanical_capacity',
        'net_mechanical_capacity',
        'manual_capacity',
        'net_manual_capacity',
        'mule_factor',
        'sample_resource',
    ];
}