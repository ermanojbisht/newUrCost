<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ratecard extends Model
{
    use HasFactory;

    protected $table = 'rate_cards';

    protected $fillable = [
        'rate_card_code',
        'name',
        'group_id',
        'description',
        'created_by',
        'updated_by',
    ];

    public function rateCardGroup()
    {
        return $this->belongsTo(RateCardGroup::class, 'group_id');
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
