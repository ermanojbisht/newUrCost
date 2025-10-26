<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sor extends Model
{
    use HasFactory;

    protected $table = 'sors';

    protected $fillable = [
        'name',
        'is_locked',
        'filename',
        'display_details',
        'short_name',
        'created_by',
        'updated_by',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
