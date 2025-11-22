<?php

namespace Database\Seeders;

use App\Models\Rate;
use App\Models\Resource;
use App\Models\RateCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $seededResources = Resource::all()->pluck('id')->toArray();
        $seededRatecards = Ratecard::all()->pluck('id')->toArray();

        $legacyRates = DB::connection('legacy_mysql')
                        ->table('rate')
                        ->whereIn('resourceid', $seededResources)
                        ->whereIn('ratecard', $seededRatecards)
                        ->get();

        foreach ($legacyRates as $legacyRate) {
            Rate::firstOrCreate([
                'resource_id'     => $legacyRate->resourceid,
                'rate_card_id'    => $legacyRate->ratecard,
                'applicable_date' => $legacyRate->appdate,
            ], [
                'unit_id'      => $legacyRate->unit??140,
                'rate'         => $legacyRate->rate,
                'valid_from'   => Carbon::createFromTimestamp($legacyRate->predate),
                'valid_to'     => Carbon::createFromTimestamp($legacyRate->postdate),
                'remarks'      => $legacyRate->remark ?? null,
                'is_locked'    => $legacyRate->locked,
                'published_at' => $legacyRate->publish_date !== '0000-00-00 00:00:00'
                                    ? $legacyRate->publish_date : null,
                'tax'          => $legacyRate->tax,
                'created_at'   => $legacyRate->insert_date !== '0000-00-00 00:00:00'
                                    ? $legacyRate->insert_date : null,
                'updated_at'   => $legacyRate->modify_date !== '0000-00-00 00:00:00'
                                    ? $legacyRate->modify_date : null,
                'created_by'   => 1,
                'updated_by'   => 1,
            ]);

        }
    }
}
