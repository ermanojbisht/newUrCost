<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadDistance extends Model
{
    use HasFactory;

    protected $table = 'lead_distances';

    protected $fillable = [
        'resource_id',
        'rate_card_id',
        'distance',
        'type',
        'applicable_date',
        'valid_from',
        'valid_to',
        'is_locked',
        'is_canceled',
    ];

    protected $casts = [
        'applicable_date' => 'date',
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
}