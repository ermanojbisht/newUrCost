<?php

namespace Database\Seeders;

use App\Models\RateCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class RateCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        DB::table('ratecards')->truncate();
        Schema::enableForeignKeyConstraints();

        $legacyRateCards = DB::connection('legacy_mysql')->table('ratecard')->get();

        foreach ($legacyRateCards as $legacyRateCard) {
            RateCard::create([
                'id' => $legacyRateCard->ratecardid,
                'ratecardname' => $legacyRateCard->ratecardname,
            ]);
        }
    }
}
