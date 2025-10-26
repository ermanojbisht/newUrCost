<?php

namespace Database\Seeders;

use App\Models\PolRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PolRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        // Legacy database से pol rates fetch करें
        $legacyPolRates = DB::connection('legacy_mysql')
            ->table('polrate')
            ->get();

        $total = $legacyPolRates->count();
        $inserted = 0;
        $skipped = 0;

        $this->command->info("Found {$total} PolRate records to process...");

        foreach ($legacyPolRates as $legacyRecord) {
            // Duplicate check
            if (PolRate::where('id', $legacyRecord->ID)->exists()) {
                $skipped++;
                continue;
            }

            PolRate::create([
                'id' => $legacyRecord->ID,
                'rate_date' => $legacyRecord->Ddate,
                'diesel_rate' => $legacyRecord->DesilRate,
                'mobile_oil_rate' => $legacyRecord->MobileRate,
                'laborer_charges' => $legacyRecord->MazdoorCharges,
                'hiring_charges' => $legacyRecord->HiringCharges,
                'overhead_charges' => $legacyRecord->OHCharges,
                'mule_rate' => $legacyRecord->MuleRate,
                'valid_from' => Carbon::createFromTimestamp($legacyRecord->predate),
                'valid_to'   => Carbon::createFromTimestamp($legacyRecord->postdate),
                'is_locked' => (bool) $legacyRecord->locked, // Convert to boolean
                'published_at' => $legacyRecord->publish_date,
                // created_at और updated_at automatically handled by Laravel
            ]);

            $inserted++;

            if ($inserted % 100 == 0) {
                $this->command->info("Processed {$inserted}/{$total} records...");
            }
        }

        $this->command->info("PolRate migration completed!");
        $this->command->info("Inserted: {$inserted} | Skipped: {$skipped}");

        Model::reguard();
    }
}
