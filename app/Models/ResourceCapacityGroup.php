<?php

namespace App
Models;

use Illuminate
Database\nEloquent

Factories

HasFactory;
use Illuminate
Database
Eloquent

Model;

class ResourceCapacityGroup extends Model
{
    use HasFactory;

    protected $table = 'resource_capacity_groups';

    protected $fillable = [
        'name',
    ];
}