<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NestedTableItem extends Model
{
    use HasFactory;

    protected $table = 'nested_table_items';

    protected $fillable = [
        '_lft',
        '_rgt',
        'item_number',
        'sor_id',
        'is_chapter',
        'parent_id',
    ];

    protected $casts = [
        'is_chapter' => 'boolean',
    ];

    public function sor()
    {
        return $this->belongsTo(Sor::class);
    }

    public function parent()
    {
        return $this->belongsTo(NestedTableItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NestedTableItem::class, 'parent_id');
    }
}