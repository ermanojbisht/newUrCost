<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('labor_indices', function (Blueprint $table) {
            // Change is_locked from boolean (tinyint 1) to integer (tinyint 4) to support 0, 1, 2
            $table->tinyInteger('is_locked')->default(0)->comment('0: Experimental, 1: Current, 2: Old')->change();
        });
    }

    public function down()
    {
        Schema::table('labor_indices', function (Blueprint $table) {
            $table->boolean('is_locked')->default(0)->change();
        });
    }
};
