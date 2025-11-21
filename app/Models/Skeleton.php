<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skeleton extends Model
{
    use HasFactory;

    protected $table = 'skeletons';

    protected $fillable = [
        'sor_id',
        'resource_id',
        'quantity',
        'unit_id',
        'item_code',
        'resource_description',
        'sort_order',
        'valid_from',
        'valid_to',
        'is_canceled',
        'is_locked',
        'factor',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_canceled' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function sor()
    {
        return $this->belongsTo(Sor::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_code', 'item_code');
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