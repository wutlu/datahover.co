<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Faq;

class FaqTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [

        ];

        foreach ($items as $item)
        {
            Faq::updateOrCreate(
                [ 'question' => $item[0] ],
                [ 'answer' => $item[1] ]
            );
        }
    }
}
