<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignJob extends Model
{
    use HasFactory;

    protected $table = 'assign_jobs';

    protected $fillable = [
        'user_id',
        'job_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldJob()
    {
        return $this->belongsTo(OldJob::class, 'job_id');
    }
}