<?php

namespace Database\Seeders;

use App\Models\ResourceGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            'Labour Group',
            'Machine Group',
            'Material Group',
            'Carriage Group',
            'Miscellaneous Group',
        ];

        foreach ($groups as $group) {
            ResourceGroup::create(['name' => $group]);
        }
    }
}
