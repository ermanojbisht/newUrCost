<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'name',
        'code',
        'language_id',
        'alias',
        'unit_group_id',
        'conversion_factor',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function unitGroup()
    {
        return $this->belongsTo(UnitGroup::class);
    }
}