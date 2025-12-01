<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class Helper
{
    public static function interpolateQuery($sql, $bindings)
    {
        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }

}
