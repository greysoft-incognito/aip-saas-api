<?php

namespace Database\Seeders;

use App\Models\CurrentPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrentPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CurrentPrice::query()->create([
            'item' => 'Maize',
            'price' => 45000,
            'unit' => 'bags',
            'available_qty' => 10050
        ]);
        CurrentPrice::query()->create([
            'item' => 'Rice',
            'price' => 45000,
            'unit' => 'bags',
            'available_qty' => 3500
        ]);
    }
}
