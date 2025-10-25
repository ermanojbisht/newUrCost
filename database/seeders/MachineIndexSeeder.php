<?php

namespace Database\Seeders;

use App\Models\MachineIndex;
use App\Models\Resource;
use App\Models\Ratecard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class MachineIndexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        DB::table('machine_indices')->truncate();
        Schema::enableForeignKeyConstraints();

        $seededResources = Resource::all()->pluck('id')->toArray();
        $seededRatecards = Ratecard::all()->pluck('id')->toArray();

        $legacyMachineIndices = DB::connection('legacy_mysql')
                                ->table('machindex')
                                ->whereIn('ResID', $seededResources)
                                ->whereIn('RateCardID', $seededRatecards)
                                ->get();

        foreach ($legacyMachineIndices as $legacyMachineIndex) {
            MachineIndex::create([
                'resource_id' => $legacyMachineIndex->ResID,
                'ratecard_id' => $legacyMachineIndex->RateCardID,
                'percent_index' => $legacyMachineIndex->perIndex,
            ]);
        }
    }
}
