<?php

namespace Database\Seeders;

use App\Models\LaborIndex;
use App\Models\LeadDistance;
use App\Models\MachineIndex;
use App\Models\Ohead;
use App\Models\OverheadMaster;
use App\Models\PolRate;
use App\Models\Rate;
use App\Models\Ratecard;
use App\Models\RegionIndexing;
use App\Models\Resource;
use App\Models\ResourceGroup;
use App\Models\Subitem;
use App\Models\TruckSpeed;
use App\Models\Unit;
use App\Models\UnitGroup;
use App\Models\User;
use Database\Seeders\RateSeeder;
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

        //isolated

        if (\App\Models\ResourceGroup::count() === 0) {
            $seedersToCall[] = ResourceGroupSeeder::class;
        }

         if (UnitGroup::count() === 0) {
            $seedersToCall[] = UnitGroupSeeder::class;
        }

        if (Unit::count() === 0) {
            $seedersToCall[] = UnitSeeder::class;
        }

        if (ResourceCapacityRule::count() === 0) {
            $seedersToCall[] = ResourceCapacityRuleSeeder::class;
        }

        if (Ratecard::count() === 0) {
            $seedersToCall[] = RateCardSeeder::class;
        }

        if (PolRate::count() === 0) {
            $seedersToCall[] = PolRateSeeder::class;
        }

        if (OverheadMaster::count() === 0) {
            $seedersToCall[] = OverheadMasterSeeder::class;
        }

        if (TruckSpeed::count() === 0) {
            $seedersToCall[] = TruckSpeedSeeder::class;
        }

        //dependent

        if (\App\Models\Sor::count() === 0) {
            $seedersToCall[] = SorSeeder::class;
        }

        if (\App\Models\Item::count() === 0) {
            $seedersToCall[] = ItemSeeder::class;
        }

        if (Resource::count() === 0) {
            $seedersToCall[] = ResourceSeeder::class;
        }

        //only after ratecard and resource table
        if (LaborIndex::count() === 0) {
            $seedersToCall[] = LaborIndexSeeder::class;
        }
        //only after ratecard and resource table
        if (MachineIndex::count() === 0) {
            $seedersToCall[] = MachineIndexSeeder::class;
        }




        if (\App\Models\Skeleton::count() === 0) {
            $seedersToCall[] = SkeletonSeeder::class;
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









        if (RegionIndexing::count() === 0) {
            $seedersToCall[] = RegionIndexingSeeder::class;
        }

        if (User::count() === 0) {
            $seedersToCall[] = UserSeeder::class;
            $seedersToCall[] = SuperAdminSeeder::class;

            DB::table('users')->insert([
                'id' => 4,
                'name' => 'Seeder User',
                'email' => 'seeder@example.com',
                'password' => bcrypt('password'),
            ]);


            DB::table('users')->insert([
                'id' => 5,
                'name' => 'Seeder User5',
                'email' => 'seeder5@example.com',
                'password' => bcrypt('password'),
            ]);

            DB::table('users')->insert([
                'id' => 7,
                'name' => 'Seeder User7',
                'email' => 'seeder7@example.com',
                'password' => bcrypt('password'),
            ]);

            //1-6 user id is needed
        }

        $this->call($seedersToCall);
    }
}
