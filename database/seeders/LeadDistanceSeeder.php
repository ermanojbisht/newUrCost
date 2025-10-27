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
use Carbon\Carbon;
use Log;

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
                'rate_card_id' => $legacyLeadDistance->RateCardID,
                'distance' => $legacyLeadDistance->Lead,
                'type' => $legacyLeadDistance->leadType,
                'applicable_date' => $legacyLeadDistance->appdate,
                'valid_from' => Carbon::createFromTimestamp($legacyLeadDistance->predate),
                'valid_to'   => Carbon::createFromTimestamp($legacyLeadDistance->postdate),
                'is_canceled' => $legacyLeadDistance->canceled,
                'is_locked' => $legacyLeadDistance->locked,
            ]);
        }
    }
}
