<?php

namespace Database\Seeders;

use App\Models\PolSkeleton;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Log;

class PolSkeletonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $legacyResources = DB::connection('legacy_mysql')->table('polskeleton')->get();

        foreach ($legacyResources as $legacyResource) {
            if (PolSkeleton::where('id', $legacyResource->ID)->exists()) {
                continue;
            }
            PolSkeleton::create([
                'id' => $legacyResource->ID,
                'date' => $legacyResource->Ddate,
                'diesel_mileage' => $legacyResource->DesilMailage,
                'mobile_oil_mileage' => $legacyResource->MobileMailage,
                'number_of_laborers' => $legacyResource->NoofMazdoors,
                'valid_from' => Carbon::createFromTimestamp($legacyResource->predate),
                'valid_to'   => Carbon::createFromTimestamp($legacyResource->postdate),
                'is_locked' => $legacyResource->locked,

            ]);
        }
    }
}
