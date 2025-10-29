<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Ohead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Log;

class OheadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        /*Schema::disableForeignKeyConstraints();
        DB::table('oheads')->truncate();
        Schema::enableForeignKeyConstraints();*/

        $legacyOheads = DB::connection('legacy_mysql')
                            ->table('ohead')
                            ->get();
        $existingUsers = User::pluck('id')->toArray();

        foreach ($legacyOheads as $legacyOhead) {
            $createdBy = in_array($legacyOhead->created_by, $existingUsers)
                        ? $legacyOhead->created_by
                        : null;

            $updatedBy = in_array($legacyOhead->modify_by, $existingUsers)
                        ? $legacyOhead->modify_by
                        : null;
            Ohead::updateOrCreate(
                ['id' => $legacyOhead->ID], // Unique key to match existing row
                [
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
                    'created_by' => $createdBy,
                    'updated_by' => $updatedBy,
                    'based_on_id' => $legacyOhead->BasedonID,
                ]
            );
        }

    }
}
