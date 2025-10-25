<?php

namespace App\\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManMuleCartRule extends Model
{
    use HasFactory;

    protected $table = 'man_mule_cart_rules';

    protected $fillable = [
        'distance',
        'calculation_method',
        'factor',
    ];
}