<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckSpeed extends Model
{
    use HasFactory;

    protected $table = 'truck_speeds';

    protected $primaryKey = 'lead_distance';

    public $incrementing = false;

    protected $fillable = [
        'lead_distance',
        'average_speed',
    ];
}