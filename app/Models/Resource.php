<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resources';

    protected $fillable = [
        'name',
        'resource_code',
        'group_id',
        'secondary_code',
        'unit_group_id',
        'unit_id',
        'description',
        'items_using_count',
        'resource_capacity_rule_id',
        'capacity_group_id',
        'dsr_code',
        'is_canceled',
        'created_by',
        'updated_by',
    ];

    public function unitGroup()
    {
        return $this->belongsTo(UnitGroup::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function resourceCapacityRule()
    {
        return $this->belongsTo(ResourceCapacityRule::class, 'resource_capacity_rule_id');
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
