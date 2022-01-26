<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Feed;
use App\Jobs\FeedsJob;

use Etsetra\Library\DateTime as DT;

class FeedGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feeds:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It creates feed files created by users at regular intervals.';

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
        $feeds = Feed::whereHas('user', function ($query) {
            return $query->where('subscription_end_date', '>=', (new DT)->nowAt());
        })->get();

        if (count($feeds))
        {
            foreach ($feeds as $feed)
            {
                $this->info($feed->name);

                FeedsJob::dispatch($feed)->onQueue('feeds');
            }
        }
        else
            $this->error('It\'s time to process Feed not found.');
    }
}
