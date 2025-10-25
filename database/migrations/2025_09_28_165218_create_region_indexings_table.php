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
        Schema::create('region_indexings', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: regname, New Column Name: region_name, Data Type: string, Description: The name of the region.
            $table->string('region_name')->unique();
            // Old Column Name: percentage, New Column Name: index_value, Data Type: decimal, Description: The index value as a percentage.
            $table->decimal('index_value', 5, 2); // Assuming a percentage value, e.g., 99.99
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region_indexings');
    }
};