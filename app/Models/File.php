<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'title',
        'filename',
        'status',
        'document_type',
        'rate_card_id',
        'sor_id',
        'created_by',
    ];

    public function rateCard()
    {
        return $this->belongsTo(RateCard::class, 'rate_card_id');
    }

    public function sor()
    {
        return $this->belongsTo(Sor::class, 'sor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
