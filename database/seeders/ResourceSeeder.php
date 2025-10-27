<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        // DO NOT truncate table
        // Schema::disableForeignKeyConstraints();
        // DB::table('resources')->truncate();
        // Schema::enableForeignKeyConstraints();

        $legacyResources = DB::connection('legacy_mysql')->table('resource')->get();

        foreach ($legacyResources as $legacyResource) {
            if (Resource::where('id', $legacyResource->code)->exists()) {
                continue;
            }
            Resource::create([
                'name' => $legacyResource->name,
                'id' => $legacyResource->code,
                'resource_group_id' => $legacyResource->resgr,
                'secondary_code' => $legacyResource->resCode,
                'unit_group_id' => $legacyResource->UnitGrpId,
                'unit_id' => $legacyResource->TechUnitID,
                'description' => $legacyResource->description,
                'items_using_count' => $legacyResource->numItemUsed ?? 0,
                'resource_capacity_rule_id' => $legacyResource->resCapacityGr,
                'resource_capacity_group_id' => $legacyResource->resCapacityGrId,
                'dsr_code' => $legacyResource->dsrcode,
                'is_canceled' => $legacyResource->canceled,
                'created_at' => $legacyResource->insert_date,
                'updated_at' => $legacyResource->modify_date,
                'created_by' => $legacyResource->created_by,
                'updated_by' => $legacyResource->modify_by,
            ]);
        }
    }
}
