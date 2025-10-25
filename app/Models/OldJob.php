<?php

namespace App
Models;

use Illuminate
Database
Eloquent
Model;

class OldJob extends Model
{
    protected $table = 'old_jobs';

    protected $fillable = [
        'title',
        'page',
        'type',
        'sort_order',
    ];
}