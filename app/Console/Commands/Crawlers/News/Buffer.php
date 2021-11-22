<?php

namespace App\Console\Commands\Crawlers\News;

use Illuminate\Console\Command;

use Etsetra\Library\DateTime as DT;

use App\Models\DataPool;

use App\Jobs\Crawlers\News\NewsTakerJob;

class Buffer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:buffer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It sends requests again for news that cannot be collected.';

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
                        [
                            'terms' => [
                                'status' => [ 'buffer' ]
                            ]
                        ],
                        [
                            'range' => [
                                'called_at' => [
                                    'lte' => (new DT)->nowAt('-10 minutes')
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'from' => 0,
                'size' => 1000,
                'sort' => [
                    [
                        'created_at' => [
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
                $this->line($item['link']);

                NewsTakerJob::dispatch($item['link'])->onQueue('newsTaker');

                $this->info('- ok -');
            }

            $this->info(count($items->source).' requests have been sent.');
        }
        else
            $this->error('Failed to establish connection to elasticsearch.');
    }
}
