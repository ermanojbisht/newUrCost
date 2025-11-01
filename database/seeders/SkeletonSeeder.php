<?php

namespace Database\Seeders;

use App\Models\Skeleton;
use App\Models\Item;
use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;

class SkeletonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        /*Schema::disableForeignKeyConstraints();
        DB::table('skeletons')->truncate();
        Schema::enableForeignKeyConstraints();*/

        $seededItems = Item::all()->pluck('item_code')->toArray(); // Map new_id to old_itemcode
        $seededResources = Resource::all()->pluck('id')->toArray();

        $legacySkeletons = DB::connection('legacy_mysql')
                            ->table('skeleton')
                            ->whereIn('raitemid', array_values($seededItems)) // Filter by legacy itemcodes
                            ->whereIn('resourceid', $seededResources)
                            ->get();

        foreach ($legacySkeletons as $legacySkeleton) {

                Skeleton::updateOrCreate(
                ['id' => $legacySkeleton->ID], // Unique key to match existing row
                [
                    'item_code' => $legacySkeleton->raitemid,
                    'sor_id' => $legacySkeleton->sorid,
                    'resource_id' => $legacySkeleton->resourceid,
                    'quantity' => $legacySkeleton->quantity,
                    'unit_id' => $legacySkeleton->unit, // Assuming 'unit' in legacy maps to 'unit_id'
                    'valid_from' => Carbon::createFromTimestamp($legacySkeleton->predate),
                    'valid_to'   => Carbon::createFromTimestamp($legacySkeleton->postdate),
                    'is_locked' => (bool) $legacySkeleton->locked, // Convert to boolean
                    'is_canceled' => (bool) $legacySkeleton->canceled, // Convert to boolean
                    'resource_description' => $legacySkeleton->res_desc,
                    'sort_order' => $legacySkeleton->SrNo,
                    'factor' => $legacySkeleton->factor,
                    'created_by' => $legacySkeleton->created_by,
                    'updated_by' => $legacySkeleton->modify_by,
                    'created_at' => $legacySkeleton->insert_date,
                    'updated_at' => $legacySkeleton->modify_date,
                ]);

        }
    }
}
