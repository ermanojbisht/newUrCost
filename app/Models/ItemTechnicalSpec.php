<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemTechnicalSpec extends Model
{
    protected $fillable = [
        'item_id',
        'introduction',
        'specifications',
        'tests_frequency',
        'dos_donts',
        'execution_sequence',
        'precautionary_measures',
        'reference_links',
    ];

    protected $casts = [
        'specifications' => 'array',
        'tests_frequency' => 'array',
        'dos_donts' => 'array',
        'execution_sequence' => 'array',
        'precautionary_measures' => 'array',
        'reference_links' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
