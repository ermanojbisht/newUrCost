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
        Schema::create('units', function (Blueprint $table) {
            // Old Column Name: ID, New Column Name: id, Data Type: bigIncrements, Description: Primary key for the table.
            $table->bigIncrements('id');
            // Old Column Name: vUnitName, New Column Name: name, Data Type: string, Description: The name of the unit.
            $table->string('name');
            // Old Column Name: vUnitCode, New Column Name: code, Data Type: string, Description: The code of the unit.
            $table->string('code');
            // Old Column Name: LanguageId, new coloumn not needed

            // Old Column Name: Alias, New Column Name: alias, Data Type: string, Description: An alias for the unit.
            $table->string('alias')->nullable();
            // Old Column Name: iUnitgrpID, New Column Name: unit_group_id, Data Type: unsignedBigInteger, Description: The ID of the unit group.
            $table->unsignedBigInteger('unit_group_id')->nullable();
            // Old Column Name: nConFac, New Column Name: conversion_factor, Data Type: decimal, Description: The conversion factor.
            $table->decimal('conversion_factor', 15, 4)->nullable(); // Assuming 15 total digits, 4 after decimal
            $table->timestamps();
            // unit_group_id foreign key will be added after unit_groups table is migrated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
