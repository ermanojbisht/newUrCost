<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobGroup extends Model
{
    use HasFactory;

    protected $table = 'job_groups';

    protected $fillable = [
        'title',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(JobGroup::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(JobGroup::class, 'parent_id');
    }
}