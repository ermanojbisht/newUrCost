<?php

namespace Database\Seeders;

use App\Models\LaborIndex;
use App\Models\Resource;
use App\Models\Ratecard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class LaborIndexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        DB::table('labor_indices')->truncate();
        Schema::enableForeignKeyConstraints();

        $seededResources = Resource::all()->pluck('id')->toArray();
        $seededRatecards = Ratecard::all()->pluck('id')->toArray();

        $legacyLaborIndices = DB::connection('legacy_mysql')
                                ->table('laborindex')
                                ->whereIn('ResID', $seededResources)
                                ->whereIn('RateCardID', $seededRatecards)
                                ->get();

        foreach ($legacyLaborIndices as $legacyLaborIndex) {
            LaborIndex::create([
                'resource_id' => $legacyLaborIndex->ResID,
                'ratecard_id' => $legacyLaborIndex->RateCardID,
                'percent_index' => $legacyLaborIndex->perIndex,
            ]);
        }
    }
}
