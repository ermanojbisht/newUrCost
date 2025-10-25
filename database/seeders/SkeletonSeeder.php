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

class SkeletonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        Schema::disableForeignKeyConstraints();
        DB::table('skeletons')->truncate();
        Schema::enableForeignKeyConstraints();

        $seededItems = Item::all()->pluck('itemcode', 'id')->toArray(); // Map new_id to old_itemcode
        $seededResources = Resource::all()->pluck('id')->toArray();

        $legacySkeletons = DB::connection('legacy_mysql')
                            ->table('skeleton')
                            ->whereIn('raitemid', array_values($seededItems)) // Filter by legacy itemcodes
                            ->whereIn('resourceid', $seededResources)
                            ->get();

        foreach ($legacySkeletons as $legacySkeleton) {
            // Find the new item_id based on the legacy raitemid
            $newItemId = array_search($legacySkeleton->raitemid, $seededItems);

            if ($newItemId !== false) {
                Skeleton::create([
                    'item_id' => $newItemId,
                    'resource_id' => $legacySkeleton->resourceid,
                    'quantity' => $legacySkeleton->quantity,
                    'unit_id' => $legacySkeleton->unit, // Assuming 'unit' in legacy maps to 'unit_id'
                ]);
            }
        }
    }
}
