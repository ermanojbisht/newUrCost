<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SubitemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('subitems')->truncate();

        $old_subitems = DB::connection('legacy_mysql')->table('subitem')->get();

        foreach ($old_subitems as $old_subitem) {
            DB::table('subitems')->insert([
                'id' => $old_subitem->ID,
                'item_code' => $old_subitem->raitemid,
                'sub_item_code' => $old_subitem->subraitem,
                'quantity' => $old_subitem->dResQty,
                'percentage' => $old_subitem->Percentage,
                'based_on_id' => $old_subitem->BasedonID,
                'sort_order' => $old_subitem->SrNo,
                'unit_id' => $old_subitem->UnitID,
                'remarks' => $old_subitem->Remark,
                'valid_from' => $old_subitem->predate ? date('Y-m-d', $old_subitem->predate) : null,
                'valid_to' => $old_subitem->postdate ? date('Y-m-d', $old_subitem->postdate) : null,
                'factor' => $old_subitem->factor,
                'created_at' => $old_subitem->insert_date,
                'updated_at' => $old_subitem->modify_date,
                'created_by' => $old_subitem->created_by,
                'updated_by' => $old_subitem->modify_by,
            ]);
        }

        Schema::enableForeignKeyConstraints();
    }
}
