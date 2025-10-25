<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RegionIndexingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('region_indexings')->truncate();

        $old_region_indexings = DB::connection('legacy_mysql')->table('regionindexing')->get();

        foreach ($old_region_indexings as $old_region_indexing) {
            DB::table('region_indexings')->updateOrInsert(
                ['id' => $old_region_indexing->ID],
                [
                    'region_name' => $old_region_indexing->regname,
                    'index_value' => $old_region_indexing->percentage,
                ]
            );
        }

        Schema::enableForeignKeyConstraints();
    }
}
