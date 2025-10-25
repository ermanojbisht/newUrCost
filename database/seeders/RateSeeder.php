<?php

namespace Database\Seeders;

use App\Models\Rate;
use App\Models\Resource;
use App\Models\Ratecard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        DB::table('rates')->truncate();
        Schema::enableForeignKeyConstraints();

        $seededResources = Resource::all()->pluck('id')->toArray();
        $seededRatecards = Ratecard::all()->pluck('id')->toArray();

        $legacyRates = DB::connection('legacy_mysql')
                        ->table('rate')
                        ->whereIn('resourceid', $seededResources)
                        ->whereIn('ratecard', $seededRatecards)
                        ->get();

        foreach ($legacyRates as $legacyRate) {
            Rate::create([
                'resource_id' => $legacyRate->resourceid,
                'ratecard_id' => $legacyRate->ratecard,
                'rate' => $legacyRate->rate,
                'unit_id' => $legacyRate->unit,
                'created_by' => $legacyRate->created_by ?? null,
                'modify_by' => $legacyRate->modify_by ?? null,
                'predate' => $legacyRate->predate,
                'postdate' => $legacyRate->postdate !== '0000-00-00 00:00:00' ? $legacyRate->postdate : null,
                'remark' => $legacyRate->remark ?? null,
                'locked' => $legacyRate->locked,
                'publish_date' => $legacyRate->publish_date !== '0000-00-00 00:00:00' ? $legacyRate->publish_date : null,
                'created_at' => $legacyRate->insert_date !== '0000-00-00 00:00:00' ? $legacyRate->insert_date : null,
                'updated_at' => $legacyRate->modify_date !== '0000-00-00 00:00:00' ? $legacyRate->modify_date : null,
            ]);
        }
    }
}