<?php

namespace App\Console\Commands\Crawlers\News;

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
    protected $signature = 'news:minuter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It determines the interval at which it will go to news sites.';

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
            ->where('source', 'news')
            ->where('type', 'page')
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
                                    [ 'match' => [ 'site' => explode('/', $track->value)[0] ] ]
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
                        $minute = intval(360 / ($total > 0 ? $total : 1));

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
