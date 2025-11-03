<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RebuildItemTreesManuallyStepByStep extends Command
{
    protected $signature = 'app:rebuild-item-trees-manually-step-by-step {sor_id?}';
    protected $description = 'Manually rebuild the nested set tree for a specific SOR or all SORs, step-by-step';
    protected $counter;

    public function handle()
    {
        $sorId = $this->argument('sor_id');

        if ($sorId) {
            $sorIds = [$sorId];
            $this->info("Starting to rebuild item tree for SOR ID: {$sorId}...");
        } else {
            $sorIds = DB::table('sors')->pluck('id');
            $this->info('Starting to rebuild all item trees manually (step-by-step)...');
        }

        foreach ($sorIds as $id) {
            $this->info("\nProcessing SOR ID: {$id}");
            $this->counter = 1;
            $roots = Item::where('sor_id', $id)->whereNull('parent_id')->get();

            foreach ($roots as $root) {
                $this->traverse($root, 0);
            }
        }

        $this->info('\nAll item trees have been rebuilt successfully!');

        return 0;
    }

    protected function traverse($node, $depth)
    {
        // Set the left value and depth
        $node->lft = $this->counter++;
        $node->depth = $depth;

        // Recursively traverse children
        $children = $node->children()->orderBy('order_in_parent')->get();
        foreach ($children as $child) {
            $this->traverse($child, $depth + 1);
        }

        // Set the right value
        $node->rgt = $this->counter++;
        $node->save();
    }
}
