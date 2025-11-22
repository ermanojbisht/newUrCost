<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourceVolumeWeightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oldResources = DB::connection('legacy_mysql')->table('resource')->get();

        foreach ($oldResources as $oldResource) {
            $volumeOrWeight = 0;
            if ($oldResource->byVolumeWeight == 1158) {
                $volumeOrWeight = 1;
            } elseif ($oldResource->byVolumeWeight == 1159) {
                $volumeOrWeight = 2;
            }
            if($volumeOrWeight == 0){
                continue;
            }

            DB::table('resources')
                ->where('id', $oldResource->code)
                ->update(['volume_or_weight' => $volumeOrWeight]);
        }
    }
}
