<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaborIndex extends Model
{
    use HasFactory;

    protected $table = 'labor_indices';

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
        'is_locked' => 'boolean',
        'is_canceled' => 'boolean',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function rateCard()
    {
        return $this->belongsTo(Ratecard::class, 'rate_card_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
