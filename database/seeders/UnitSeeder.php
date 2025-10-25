<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assuming 'legacy_mysql' is configured in config/database.php for the old database
        $oldUnits = DB::connection('legacy_mysql')->table('units')->get(); // Assuming old units table is named 'units'

        foreach ($oldUnits as $oldUnit) {
            // Check if a row with the same ID already exists
            if (!DB::table('units')->where('id', $oldUnit->ID)->exists()) {
                DB::table('units')->insert([
                    'id' => $oldUnit->ID, // Old: ID, New: id
                    'name' => $oldUnit->vUnitName, // Old: vUnitName, New: name
                    'code' => $oldUnit->vUnitCode, // Old: vUnitCode, New: code
                    'alias' => $oldUnit->Alias, // Old: Alias, New: alias
                    'unit_group_id' => $oldUnit->iUnitgrpID, // Old: iUnitgrpID, New: unit_group_id
                    'conversion_factor' => $oldUnit->nConFac, // Old: nConFac, New: conversion_factor
                    'created_at' => now(), // Assuming no created_at in old units table
                    'updated_at' => now(), // Assuming no updated_at in old units table
                ]);
            }
        }
    }
}