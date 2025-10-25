<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assuming 'legacy_mysql' is configured in config/database.php for the old database
        $oldSors = DB::connection('legacy_mysql')->table('sor')->get();

        foreach ($oldSors as $oldSor) {
            // Check if a row with the same ID already exists
            if (!DB::table('sors')->where('id', $oldSor->sorid)->exists()) {
                DB::table('sors')->insert([
                    'id' => $oldSor->sorid, // Old: sorid, New: id
                    'name' => $oldSor->sorname, // Old: sorname, New: name
                    'is_locked' => $oldSor->locked, // Old: locked, New: is_locked
                    'display_details' => $oldSor->display_details, // Old: display_details, New: display_details
                    'filename' => $oldSor->filename, // Old: filename, New: filename
                    'short_name' => $oldSor->shortname, // Old: shortname, New: short_name
                    'created_at' => $oldSor->insert_date, // Old: insert_date, New: created_at
                    'created_by' => 1, // Old: created_by, New: created_by
                    'updated_at' => $oldSor->modify_date, // Old: modify_date, New: updated_at
                    'updated_by' => $oldSor->modify_by, // Old: modify_by, New: updated_by
                ]);
            }
        }
    }
}
