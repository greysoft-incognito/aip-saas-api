<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        if (\App\Models\User::where('role', 'admin')->doesntExist()) {
            \App\Models\User::factory()->create([
                 'name' => 'Test User',
                 'email' => 'test@example.com',
            ]);
        }
        
        $this->call([
            AnnouncementSeeder::class,
            CurrentPriceSeeder::class,
            DiseaseOutbreakSeeder::class,
            EventSeeder::class,
            MarketItemSeeder::class,
            SlideSeeder::class,
            SoilRequirementSeeder::class,
        ]);
    }
}
