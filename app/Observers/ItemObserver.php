<?php

namespace App\Observers;

use App\Models\Item;
use Illuminate\Support\Facades\Log;

class ItemObserver
{
    public function saving(Item $item)
    {
        //if ($item->item_type == 3) {
            //Log::info("Observer saving item ID: {$item->id}");

            $ancestors = $item->ancestors()->where('sor_id', $item->sor_id)->get();
            //Log::info("Found {$ancestors->count()} ancestors.");

            $nameParts = [];
            foreach ($ancestors as $ancestor) {
                // Skip the root node (which has no parent) and ensure ancestor type is 1 or 2
                if ($ancestor->parent_id !== null && in_array($ancestor->item_type, [1, 2])) {
                    $nameParts[] = $ancestor->description;
                    //Log::info("Using ancestor ID: {$ancestor->id}, Description: {$ancestor->description}");
                }
            }

            $nameParts[] = $item->description;
            $item->name = implode("\n", $nameParts);
            //Log::info("Generated name: {$item->name}");
        /*} else {
            $item->name = $item->description;
        }*/
    }
}
