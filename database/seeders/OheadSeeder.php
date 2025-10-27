<?php

namespace Database\Seeders;

use App\Models\Ohead;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;

class OheadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        DB::table('oheads')->truncate();
        Schema::enableForeignKeyConstraints();

        $seededItems = Item::all()->pluck('id')->toArray(); // Map old_itemcode to new_id

        $legacyOheads = DB::connection('legacy_mysql')
                            ->table('ohead')
                            //->whereIn('raitemid', array_keys($seededItems)) // Filter by legacy itemcodes
                            ->get();
        Log::info("OheadSeeder  legacyOheads count= ".print_r($legacyOheads->count(),true));

        foreach ($legacyOheads as $legacyOhead) {
            //$newItemId = $seededItems[$legacyOhead->raitemid] ?? null;

            //if ($newItemId) {
                Ohead::create([
                    'id' => $legacyOhead->ID,
                    'item_id' => $legacyOhead->raitemid,
                    'overhead_id' => $legacyOhead->oheadid,
                    'calculation_type' => $legacyOhead->oon,
                    'parameter' => $legacyOhead->paramtr,
                    'sort_order' => $legacyOhead->sorder,
                    'applicable_items' => $legacyOhead->onitm,
                    'description' => $legacyOhead->ohdesc,
                    'allow_further_overhead' => $legacyOhead->furtherOhead,
                    'valid_from' => Carbon::createFromTimestamp($legacyOhead->predate),
                    'valid_to'   => Carbon::createFromTimestamp($legacyOhead->postdate),
                    'is_canceled' => $legacyOhead->canceled,
                    'created_by' => $legacyOhead->created_by,
                    'updated_by' => $legacyOhead->modify_by,
                    'based_on_id' => $legacyOhead->BasedonID,
                ]);
            //}
        }
    }
}
