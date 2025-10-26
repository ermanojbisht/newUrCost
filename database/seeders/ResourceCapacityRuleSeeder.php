<?php

namespace Database\Seeders;

use App\Models\ResourceCapacityRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class ResourceCapacityRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $legacyResources = DB::connection('legacy_mysql')->table('rescaprules')->get();

        foreach ($legacyResources as $legacyResource) {
            if (ResourceCapacityRule::where('id', $legacyResource->groupId)->exists()) {
                continue;
            }
            ResourceCapacityRule::create([
                'id' => $legacyResource->groupId, // Old primary key को preserve करें
                'mechanical_capacity' => $legacyResource->MechCapacity,
                'net_mechanical_capacity' => $legacyResource->MechNetCapacity,
                'manual_capacity' => $legacyResource->ManCapacity,
                'net_manual_capacity' => $legacyResource->ManNetCapacity,
                'mule_factor' => $legacyResource->MuleFactor,
                'sample_resource' => $legacyResource->sampleResource,
            ]);
        }
    }
}
