<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('machine_indices', function (Blueprint $table) {
            $table->tinyInteger('is_locked')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_indices', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->change();
        });
    }
};
