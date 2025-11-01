<?php

namespace App\Observers;

use App\Models\Item;

class ItemObserver
{
    /**
     * Handle the Item "saving" event.
     *
     * @param  \App\Models\Item  $item
     * @return void
     */
    public function saving(Item $item)
    {
        if ($item->item_type === 3) {
            // For schedulable items, generate the full hierarchical name.
            $ancestors = $item->ancestors;
            $nameParts = $ancestors->pluck('description');
            $nameParts->push($item->description);
            $item->name = $nameParts->join("\n");
        } else {
            // For SOR Root (1) or Chapter (2), just use the item\'s own description.
            $item->name = $item->description;
        }
    }
}
