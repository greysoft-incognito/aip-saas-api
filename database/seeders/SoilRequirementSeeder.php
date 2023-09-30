<?php

namespace Database\Seeders;

use App\Models\SoilRequirement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SoilRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SoilRequirement::factory(10)->create();
    }
}
