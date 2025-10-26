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
use Carbon\Carbon;
use Log;

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

        $seededRatecards = Ratecard::all()->pluck('id')->toArray();

        $legacyLaborIndices = DB::connection('legacy_mysql')
                                ->table('laborindex')
                                ->whereIn('RateCardID', $seededRatecards)
                                ->get();

        foreach ($legacyLaborIndices as $legacyLaborIndex) {
            LaborIndex::create([
                'resource_id' => $legacyLaborIndex->ResID,
                'rate_card_id' => $legacyLaborIndex->RateCardID,
                'index_value' => $legacyLaborIndex->perIndex,
                'is_canceled' => $legacyLaborIndex->canceled,
                'is_locked' => $legacyLaborIndex->locked,
                'valid_from' => Carbon::createFromTimestamp($legacyLaborIndex->predate),
                'valid_to'   => Carbon::createFromTimestamp($legacyLaborIndex->postdate),
            ]);
        }
    }
}
