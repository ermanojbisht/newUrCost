<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OverheadMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('overhead_masters')->truncate();

        $old_overhead_masters = DB::connection('legacy_mysql')->table('ohmaster')->get();

        foreach ($old_overhead_masters as $old_overhead_master) {
            DB::table('overhead_masters')->insert([
                'id' => $old_overhead_master->ID,
                'code' => $old_overhead_master->vOHCode,
                'flag' => $old_overhead_master->Flag,
                'description' => $old_overhead_master->vdescription,
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
