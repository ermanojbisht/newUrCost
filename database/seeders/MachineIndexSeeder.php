<?php

namespace Database\Seeders;

use App\Models\MachineIndex;
use App\Models\Resource;
use App\Models\RateCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;

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

        $seededRateCards = RateCard::all()->pluck('id')->toArray();

        $legacyMachineIndices = DB::connection('legacy_mysql')
                                ->table('machindex')
                                ->whereIn('RateCardID', $seededRateCards)
                                ->get();

        foreach ($legacyMachineIndices as $legacyMachineIndex) {
            MachineIndex::create([
                'resource_id' => $legacyMachineIndex->ResID,
                'rate_card_id' => $legacyMachineIndex->RateCardID,
                'index_value' => $legacyMachineIndex->perIndex,
                'is_canceled' => $legacyMachineIndex->canceled,
                'is_locked' => $legacyMachineIndex->locked,
                'valid_from' => Carbon::createFromTimestamp($legacyMachineIndex->predate),
                'valid_to'   => Carbon::createFromTimestamp($legacyMachineIndex->postdate),
            ]);
        }
    }
}
