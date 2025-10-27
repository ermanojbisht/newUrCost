<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ManMuleCartRule extends Model
{

    protected $table = 'man_mule_cart_rules';

    protected $fillable = [
        'distance',
        'calculation_method',
        'factor',
    ];
}
