<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruckSpeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('truck_speeds')->truncate();

        $old_truck_speeds = DB::connection('legacy_mysql')->table('speedtruck')->get();

        foreach ($old_truck_speeds as $old_truck_speed) {
            DB::table('truck_speeds')->insert([
                'lead_distance' => $old_truck_speed->LeadKm,
                'average_speed' => $old_truck_speed->AverageSpeed,
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
