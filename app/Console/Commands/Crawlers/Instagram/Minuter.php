<?php

namespace App\Console\Commands\Crawlers\Instagram;

use Illuminate\Console\Command;

use App\Models\Track;
use App\Models\DataPool;

use Etsetra\Library\DateTime as DT;

class Minuter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:minuter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It determines the interval at which it will go to instagram tracks.';

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
            ->where('source', 'instagram')
            ->where('type', 'hashtag')
            ->where('created_at', '<=', (new DT)->nowAt('-1 hours'))
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
                                    [ 'match' => [ 'site' => 'instagram.com' ] ],
			                        [
			                            'query_string' => [
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
                        $minute = intval(120 / ($total > 0 ? $total : 1));
                        $minute = $minute > 30 ? $minute : 30;

                        $this->info('minute: ['.$minute.']');

                        $track->request_frequency = $minute;
                        $track->total_data = $total;
                        $track->save();
                    }
                    else
                        $this->error('Elasticsearch connection failed.');
                }
                else
                    $this->error('Track not tracked.');

                $this->line('----');
            }
        }
    }
}
