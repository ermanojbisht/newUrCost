<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionIndexing extends Model
{
    use HasFactory;

    protected $table = 'region_indexings';

    protected $fillable = [
        'region_name',
        'index_value',
    ];
}