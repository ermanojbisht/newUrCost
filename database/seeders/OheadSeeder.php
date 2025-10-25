<?php

namespace Database\Seeders;

use App\Models\Ohead;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

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

        $seededItems = Item::all()->pluck('id', 'itemcode')->toArray(); // Map old_itemcode to new_id

        $legacyOheads = DB::connection('legacy_mysql')
                            ->table('ohead')
                            ->whereIn('raitemid', array_keys($seededItems)) // Filter by legacy itemcodes
                            ->get();

        foreach ($legacyOheads as $legacyOhead) {
            $newItemId = $seededItems[$legacyOhead->raitemid] ?? null;

            if ($newItemId) {
                Ohead::create([
                    'item_id' => $newItemId,
                    'ohead_id' => $legacyOhead->oheadid,
                    'overhead_calculation_type' => $legacyOhead->oon,
                    'parameter' => $legacyOhead->paramtr,
                    'sorder' => $legacyOhead->sorder,
                    'onitm' => $legacyOhead->onitm,
                    'ohdesc' => $legacyOhead->ohdesc,
                    'furtherOhead' => $legacyOhead->furtherOhead,
                ]);
            }
        }
    }
}
