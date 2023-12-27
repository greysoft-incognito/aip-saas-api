<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            'farmers' => [
                'cooperatives',
                'corporate',
                'outgrowers',
            ],
            'aggregators' => [
                'individuals',
                'corporate',
            ],
            'suppliers' => [
                "extension",
                "mechanization",
                "seeds",
                "fertilizers",
                "herbicides",
            ],
            'processors' => [
                'small',
                'medium',
                'large'
            ],
            'offtakers' => [
                'local',
                'international'
            ],
            'logistics' => [
            ],
            'researchers' => [
            ],
        ];

        foreach ($groups as $group => $types) {
            if (count($types)) {
                foreach ($types as $type) {
                    User::factory(2)->create([
                        'type' => $type,
                        'group' => $group,
                    ]);
                }
            } else {
                User::factory(4)->create([
                    'type' => $group,
                    'group' => $group,
                ]);
            }
        }
    }
}
