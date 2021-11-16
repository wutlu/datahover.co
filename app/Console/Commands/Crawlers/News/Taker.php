<?php

namespace App\Console\Commands\Crawlers\News;

use Illuminate\Console\Command;

use App\Models\DataPool;

use App\Jobs\Crawlers\News\NewsTakerJob;

class Taker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:taker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'News track link taker.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $items = (new DataPool)->find(
            [
                'bool' => [
                    'filter' => [
                        'terms' => [
                            'status' => [ 'call' ]
                        ]
                    ]
                ]
            ],
            [
                'from' => 0,
                'size' => 1000,
                'sort' => [
                    [
                        'called_at' => [
                            'order' => 'desc'
                        ]
                    ]
                ]
            ]
        );

        if ($items->success == 'ok')
        {
            foreach ($items->source as $item)
            {
                $this->info($item['link']);

                NewsTakerJob::dispatch($item['link'])->onQueue('taker');
            }
        }
        else
        {
            // ES bağlantısı kurulamadı. Log gönder.
        }
    }
}
