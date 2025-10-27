<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Assuming 'legacy_mysql' is configured in config/database.php for the old database
        $oldItems = DB::connection('legacy_mysql')->table('item')->orderBy('chId')->get(); // Assuming old items table is named 'item'

        foreach ($oldItems as $oldItem) {
            // Check if a row with the same ID already exists
            if (!DB::table('items')->where('id', $oldItem->chId)->exists()) {
                DB::table('items')->insert([
                    'id' => $oldItem->chId, // Old: chId, New: id
                    'sor_id' => $oldItem->sorId, // Old: sorId, New: sor_id
                    'parent_id' => ($oldItem->chParentId == -1) ? null : $oldItem->chParentId, // Old: chParentId, New: parent_id
                    'item_code' => $oldItem->itemcode, // Old: itemcode, New: item_code
                    'name' => $oldItem->itemname ?? 'Unnamed Item', // Old: itemname, New: name
                    'order_in_parent' => $oldItem->orderInParent, // Old: orderInParent, New: order_in_parent
                    'specification_code' => $oldItem->SpcCode, // Old: SpcCode, New: specification_code
                    'specification_page_number' => $oldItem->SpcPageNO, // Old: SpcPageNO, New: specification_page_number
                    'item_type' => $oldItem->chIdtype, // Old: chIdtype, New: item_type
                    'sort_order' => $oldItem->maybeusedforsorting, // Old: maybeusedforsorting, New: sort_order
                    'item_number' => $oldItem->ItemNo, // Old: ItemNo, New: item_number
                    'description' => $oldItem->ItemDesc, // Old: ItemDesc, New: description
                    'short_description' => $oldItem->ItemShortDesc, // Old: ItemShortDesc, New: short_description
                    'turnout_quantity' => $oldItem->TurnOutQuantity, // Old: TurnOutQuantity, New: turnout_quantity
                    'assumptions' => $oldItem->Assumption, // Old: Assumption, New: assumptions
                    'footnotes' => $oldItem->FootNote, // Old: FootNote, New: footnotes
                    'unit_id' => ($oldItem->UnitID == 0) ? null : $oldItem->UnitID, // Old: UnitID, New: unit_id
                    'is_canceled' => $oldItem->canceled, // Old: canceled, New: is_canceled
                    'nested_list_order' => $oldItem->orderFromNestedList, // Old: orderFromNestedList, New: nested_list_order
                    'sub_item_level' => $oldItem->subitemlvl, // Old: subitemlvl, New: sub_item_level
                    'sub_item_count' => $oldItem->noOfSubitem, // Old: noOfSubitem, New: sub_item_count
                    'old_item_code' => $oldItem->olditemcode, // Old: olditemcode, New: old_item_code
                    'dsr_16_id' => $oldItem->dsr16id, // Old: dsr16id, New: dsr_16_id
                    'is_locked' => $oldItem->locked, // Old: locked, New: is_locked
                    'created_at' => $oldItem->insert_date, // Old: insert_date, New: created_at
                    'created_by' => $oldItem->created_by, // Old: created_by, New: created_by
                    'updated_at' => $oldItem->modify_date, // Old: modify_date, New: updated_at
                    'updated_by' => $oldItem->modify_by, // Old: modify_by, New: updated_by
                    'reference_from' => ($oldItem->ref_from == 0) ? null : $oldItem->ref_from, // Old: ref_from, New: reference_from
                ]);
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
