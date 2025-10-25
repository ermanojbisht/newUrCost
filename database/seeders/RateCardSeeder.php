<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RateCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('rate_cards')->truncate();

        $old_rate_cards = DB::connection('legacy_mysql')->table('ratecard')->get();

        foreach ($old_rate_cards as $old_rate_card) {
            DB::table('rate_cards')->insert([
                'id' => $old_rate_card->id,
                'rate_card_code' => $old_rate_card->ratecardid,
                'name' => $old_rate_card->ratecardname,
                'description' => $old_rate_card->description,
                'created_at' => $old_rate_card->insert_date,
                'updated_at' => $old_rate_card->modify_date,
                'created_by' => $old_rate_card->created_by,
                'updated_by' => $old_rate_card->modify_by,
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
