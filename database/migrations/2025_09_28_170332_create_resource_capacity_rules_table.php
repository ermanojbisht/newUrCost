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
        Schema::create('resource_capacity_rules', function (Blueprint $table) {
            // Old Column Name: groupId, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: MechCapacity, New Column Name: mechanical_capacity, Data Type: decimal, Description: The mechanical capacity.
            $table->decimal('mechanical_capacity', 10, 4)->nullable();
            // Old Column Name: MechNetCapacity, New Column Name: net_mechanical_capacity, Data Type: decimal, Description: The net mechanical capacity.
            $table->decimal('net_mechanical_capacity', 10, 4)->nullable();
            // Old Column Name: ManCapacity, New Column Name: manual_capacity, Data Type: decimal, Description: The manual capacity.
            $table->decimal('manual_capacity', 10, 4)->nullable();
            // Old Column Name: ManNetCapacity, New Column Name: net_manual_capacity, Data Type: decimal, Description: The net manual capacity.
            $table->decimal('net_manual_capacity', 10, 4)->nullable();
            // Old Column Name: MuleFactor, New Column Name: mule_factor, Data Type: decimal, Description: The mule factor.
            $table->decimal('mule_factor', 10, 4)->nullable();
            // Old Column Name: sampleResource, New Column Name: sample_resource, Data Type: text, Description: A sample resource.
            $table->text('sample_resource')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_capacity_rules');
    }
};