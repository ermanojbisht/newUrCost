<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subitem extends Model
{
    use HasFactory;

    protected $table = 'subitems';

    protected $fillable = [
        'item_id',
        'sub_item_id',
        'quantity',
        'percentage',
        'based_on_id',
        'sort_order',
        'unit_id',
        'remarks',
        'valid_from',
        'valid_to',
        'factor',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function subItem()
    {
        return $this->belongsTo(Item::class, 'sub_item_id');
    }

    public function basedOn()
    {
        return $this->belongsTo(Subitem::class, 'based_on_id');
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
