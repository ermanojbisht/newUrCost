<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TempController extends Controller
{
    public function testUpdateName()
    {
        $a = Item::where('item_code', '6000004')->first();
        
        if (!$a) {
            return ['error' => 'Item with item_code 6000004 not found.'];
        }

        // Just save the item to trigger the observer
        $a->save();
        
        // Refresh the model to get the updated name from the database
        $a->refresh();

        return ['name' => $a->name, 'description' => $a->description];
    }
}
