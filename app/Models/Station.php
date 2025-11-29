<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = [
        'name',
        'nodal_rate_card_id',
        'nodal_resource_id',
        'resources',
        'rate_card_ids',
    ];

    protected $casts = [
        'resources' => 'array',
        'rate_card_ids' => 'array',
    ];

    public function nodalRateCard()
    {
        return $this->belongsTo(RateCard::class, 'nodal_rate_card_id');
    }

    public function nodalResource()
    {
        return $this->belongsTo(Resource::class, 'nodal_resource_id');
    }

    public function leadDistances()
    {
        return $this->hasMany(LeadDistance::class);
    }

    public function updateAssociations()
    {
        $resourceIds = $this->leadDistances()->distinct()->pluck('resource_id')->values()->toArray();
        $rateCardIds = $this->leadDistances()->distinct()->pluck('rate_card_id')->values()->toArray();

        $this->update([
            'resources' => $resourceIds,
            'rate_card_ids' => $rateCardIds,
        ]);

        return [
            'resources_count' => count($resourceIds),
            'rate_cards_count' => count($rateCardIds),
        ];
    }
}
