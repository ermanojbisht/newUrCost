<?php

namespace Database\Seeders;

use App\Models\LeadDistance;
use App\Models\Resource;
use App\Models\Ratecard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class LeadDistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        DB::table('lead_distances')->truncate();
        Schema::enableForeignKeyConstraints();

        $seededResources = Resource::all()->pluck('id')->toArray();
        $seededRatecards = Ratecard::all()->pluck('id')->toArray();

        $legacyLeadDistances = DB::connection('legacy_mysql')
                                ->table('leadDistance')
                                ->whereIn('ResID', $seededResources)
                                ->whereIn('RateCardID', $seededRatecards)
                                ->get();

        foreach ($legacyLeadDistances as $legacyLeadDistance) {
            LeadDistance::create([
                'resource_id' => $legacyLeadDistance->ResID,
                'ratecard_id' => $legacyLeadDistance->RateCardID,
                'lead' => $legacyLeadDistance->Lead,
                'lead_type' => $legacyLeadDistance->leadType,
            ]);
        }
    }
}
