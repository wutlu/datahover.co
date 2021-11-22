<?php

namespace App\Console\Commands\Crawlers\News;

use Illuminate\Console\Command;

use App\Jobs\Crawlers\News\NewsDetectorJob;

use App\Models\Track;
use Etsetra\Library\DateTime as DT;
use DB;

class Detector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:detector';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiates news tracking.';

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
            ->where('request_at', '<=', DB::raw("NOW() - INTERVAL '1 minutes' * request_frequency"))
            ->orderBy('id', 'asc')
            ->get();

        if (count($tracks))
        {
            foreach ($tracks as $track)
            {
                $this->info("Call: $track->value");

                if ($track->subscriptionEndDate() >= date('Y-m-d'))
                {
                    NewsDetectorJob::dispatch($track)->onQueue('newsDetector');

                    $track->request_at = (new DT)->nowAt();
                    $track->request_hit = $track->request_hit + 1;
                    $track->valid = true;
                    $track->save();
                }
                else
                    $this->error('This track does not belong to a current account.');
            }
        }
    }
}
