<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineIndex extends Model
{
    use HasFactory;

    protected $table = 'machine_indices';

    protected $fillable = [
        'resource_id',
        'rate_card_id',
        'index_value',
        'valid_from',
        'valid_to',
        'is_locked',
        'is_canceled',
        'created_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_locked' => 'integer',
        'is_canceled' => 'boolean',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function rateCard()
    {
        return $this->belongsTo(RateCard::class, 'rate_card_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
