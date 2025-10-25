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
        Schema::create('man_mule_cart_rules', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: km, New Column Name: distance, Data Type: decimal, Description: The distance in kilometers.
            $table->decimal('distance', 8, 2);
            // Old Column Name: byVolumeOrWeight, New Column Name: calculation_method, Data Type: integer, Description: The calculation method.
            $table->integer('calculation_method');
            // Old Column Name: factor, New Column Name: factor, Data Type: decimal, Description: The multiplication factor.
            $table->decimal('factor', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('man_mule_cart_rules');
    }
};