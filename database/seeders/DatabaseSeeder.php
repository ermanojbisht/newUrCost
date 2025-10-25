<?php

namespace Database\Seeders;

use App\Models\Rate;
use App\Models\Ratecard;
use App\Models\Resource;
use App\Models\Ohead; // Added
use App\Models\LeadDistance; // Added
use App\Models\LaborIndex; // Added
use App\Models\MachineIndex; // Added
use App\Models\Subitem; // Added
use App\Models\TruckSpeed;
use App\Models\OverheadMaster;
use App\Models\RegionIndexing;
use Database\Seeders\RateSeeder; // Added
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $seedersToCall = [];

        if (\App\Models\Sor::count() === 0) {
            $seedersToCall[] = SorSeeder::class;
        }

        if (\App\Models\Item::count() === 0) {
            $seedersToCall[] = ItemSeeder::class;
        }

        if (\App\Models\Skeleton::count() === 0) {
            $seedersToCall[] = SkeletonSeeder::class;
        }

        if (Ratecard::count() === 0) {
            $seedersToCall[] = RateCardSeeder::class;
        }

        if (Resource::count() === 0) {
            $seedersToCall[] = ResourceSeeder::class;
        }

        if (Rate::count() === 0) {
            $seedersToCall[] = RateSeeder::class;
        }

        if (Subitem::count() === 0) {
            $seedersToCall[] = SubitemSeeder::class;
        }

        if (Ohead::count() === 0) {
            $seedersToCall[] = OheadSeeder::class;
        }

        if (LeadDistance::count() === 0) {
            $seedersToCall[] = LeadDistanceSeeder::class;
        }

        if (LaborIndex::count() === 0) {
            $seedersToCall[] = LaborIndexSeeder::class;
        }

        if (MachineIndex::count() === 0) {
            $seedersToCall[] = MachineIndexSeeder::class;
        }

        if (TruckSpeed::count() === 0) {
            $seedersToCall[] = TruckSpeedSeeder::class;
        }

        if (OverheadMaster::count() === 0) {
            $seedersToCall[] = OverheadMasterSeeder::class;
        }

        if (RegionIndexing::count() === 0) {
            $seedersToCall[] = RegionIndexingSeeder::class;
        }

        $this->call($seedersToCall);
    }
}
