<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FixOverheadMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $overheadMaster = new \App\Models\OverheadMaster();
        dd($overheadMaster);
    }
}
