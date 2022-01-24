<?php

namespace App\Console\Commands\Crawlers\Twitter;

use Illuminate\Console\Command;

use App\Models\Track;
use App\Models\DataPool;

use Etsetra\Library\DateTime as DT;

class Counter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:counter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Counts how much data is collected from Twitter.';

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
        $tracks = Track::where(function($query) {
                $query->orWhere('valid', true);
                $query->orWhereNull('valid');
            })
            ->where('source', 'twitter')
            ->where('type', 'keyword')
            ->where('created_at', '<=', (new DT)->nowAt('-10 minutes'))
            ->orderBy('id', 'asc')
            ->get();

        if (count($tracks))
        {
            foreach ($tracks as $track)
            {
                $this->info("Check: $track->value");

                if ($track->subscriptionEndDate() >= date('Y-m-d'))
                {
                    $item = (new DataPool)->find(
                        [
                            'bool' => [
                                'must' => [
                                    [ 'match' => [ 'status' => 'ok' ] ],
                                    [ 'match' => [ 'site' => 'twitter.com' ] ],
                                    [
                                        'query_string' => [
                                            'fields' => [ 'title', 'text' ],
                                            'query' => $track->value,
                                            'default_operator' => 'AND'
                                        ]
                                    ]
                                ],
                                'filter' => [
                                    [
                                        'range' => [ 'called_at' => [ 'gte' => (new DT)->nowAt('-6 hours') ] ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'from' => 0,
                            'size' => 0
                        ]
                    );

                    if ($item->success == 'ok')
                    {
                        $this->info('data: ['.$item->stats['total'].']');

                        $total = $item->stats['total'];

                        $track->total_data = $total;
                        $track->save();
                    }
                    else
                        $this->error('Elasticsearch connection failed.');
                }
                else
                    $this->error('The criterion is not followed.');

                $this->line('----');
            }
        }
    }
}
