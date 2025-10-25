<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ohead extends Model
{
    use HasFactory;

    protected $table = 'oheads';

    protected $fillable = [
        'item_id',
        'overhead_id',
        'calculation_type',
        'parameter',
        'sort_order',
        'applicable_items',
        'description',
        'based_on_id',
        'valid_from',
        'valid_to',
        'is_canceled',
        'allow_further_overhead',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_canceled' => 'boolean',
        'allow_further_overhead' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function overhead()
    {
        return $this->belongsTo(OverheadMaster::class, 'overhead_id');
    }

    public function basedOn()
    {
        return $this->belongsTo(OverheadMaster::class, 'based_on_id');
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
