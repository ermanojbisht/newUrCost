<?php

namespace Database\Seeders;

use App\Models\Ratecard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class RatecardSeeder extends Seeder
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

        $legacyRatecards = DB::connection('legacy_mysql')->table('ratecard')->get();

        foreach ($legacyRatecards as $legacyRatecard) {
            Ratecard::create([
                'id' => $legacyRatecard->ratecardid,
                'ratecardname' => $legacyRatecard->ratecardname,
            ]);
        }
    }
}
