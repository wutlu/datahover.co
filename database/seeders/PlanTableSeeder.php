<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Trial',
                'price' => 0,
                'track_limit' => 1,
            ],
            [
                'name' => 'Basic',
                'price' => 59,
                'track_limit' => 10,
            ],
            [
                'name' => 'Enterprise',
                'price' => 149,
                'track_limit' => 100,
            ],
            [
                'name' => 'Company',
                'price' => 0,
                'track_limit' => 0,
            ],
        ];

        foreach ($plans as $plan)
        {
            Plan::updateOrCreate(
                [
                    'name' => $plan['name']
                ],
                [
                    'price' => $plan['price'],
                    'track_limit' => $plan['track_limit'],
                ]
            );
        }
    }
}
