<?php

namespace Database\Seeders;

use App\Models\ManMuleCartRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;


class ManMuleCartRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $legacyResources = DB::connection('legacy_mysql')->table('manMuleCartRules')->get();

        foreach ($legacyResources as $legacyResource) {
            if (ManMuleCartRule::where('id', $legacyResource->ID)->exists()) {
                continue;
            }
            ManMuleCartRule::create([
                'id' => $legacyResource->ID,
                'distance' => $legacyResource->km,
                'calculation_method' => $legacyResource->byVolumeOrWeight,  //1158,1159,3 i think by wieght/voloume for first 2 and then 3 is for mule
                'factor' => $legacyResource->factor,

            ]);
        }
    }
}
