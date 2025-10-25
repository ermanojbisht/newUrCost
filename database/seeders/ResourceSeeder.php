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

        Schema::disableForeignKeyConstraints();
        DB::table('resources')->truncate();
        Schema::enableForeignKeyConstraints();

        $legacyResources = DB::connection('legacy_mysql')->table('resource')->get();

        foreach ($legacyResources as $legacyResource) {
            Resource::create([
                'id' => $legacyResource->code,
                'name' => $legacyResource->name,
                'resource_group_id' => $legacyResource->resgr,
                'res_code' => $legacyResource->resCode,
            ]);
        }
    }
}
