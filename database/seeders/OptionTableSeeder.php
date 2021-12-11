<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Option;

class OptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $options = [
            'proxy.api_key' => '',
            'proxy.max_buy_piece' => 4,
            'proxy.min_balance_for_alert' => 10,
            'proxy.proxy_country' => 'us',
            'proxy.proxy_version' => 3,
            'proxy.buy_period' => 30,
            'proxy.current_balance' => 0,

            'twitter.status' => 'off',
            'news.status' => 'off',
            'youtube.status' => 'off',
        ];

        foreach ($options as $key => $value)
        {
            Option::firstOrCreate([ 'key' => $key ], [ 'value' => $value ]);
        }
    }
}
