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
        Schema::create('unit_groups', function (Blueprint $table) {
            // Old Column Name: ParentID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: vUnitGrpName, New Column Name: name, Data Type: string, Description: The name of the unit group.
            $table->string('name');
            // Old Column Name: BaseUnitID, New Column Name: base_unit_id, Data Type: unsignedBigInteger, Description: The ID of the base unit for the group.
            $table->unsignedBigInteger('base_unit_id')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('base_unit_id')->references('id')->on('units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_groups');
    }
};