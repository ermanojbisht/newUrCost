<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assuming 'legacy_mysql' is configured in config/database.php for the old database
        $oldUnitGroups = DB::connection('legacy_mysql')->table('unit_group')->get();

        foreach ($oldUnitGroups as $oldUnitGroup) {
            // Check if a row with the same ID already exists
            if (!DB::table('unit_groups')->where('id', $oldUnitGroup->iUnitgrpID)->exists()) {
                DB::table('unit_groups')->insert([
                    'id' => $oldUnitGroup->iUnitgrpID,
                    'name' => $oldUnitGroup->vUnitGrpName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
