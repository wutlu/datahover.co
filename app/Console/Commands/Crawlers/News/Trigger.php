<?php

namespace App\Console\Commands\Crawlers\News;

use Illuminate\Console\Command;

use App\Jobs\Crawlers\News\LinkDetectorJob;

use App\Models\Track;
use Etsetra\Library\DateTime as DT;
use DB;

class Trigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:trigger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'News track trigger.';

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
        $tracks = Track::whereHas('user', function($q) {
                $q->whereDate('users.subscription_end_date', '>=', (new DT)->nowAt());
            })
            ->where(function($query) {
                $query->orWhere('valid', true);
                $query->orWhereNull('valid');
            })
            ->where('source', 'news')
            ->where('type', 'page')
            ->where('request_at', '<=', DB::raw("NOW() - INTERVAL '1 minutes' * request_frequency"))
            ->orderBy('id', 'asc');

        $tracks->update([ 'valid' => true ]);

        $tracks = $tracks->get()->unique('value');

        foreach ($tracks as $track)
        {
            $this->info("Call: $track->value");

            LinkDetectorJob::dispatch($track)->onQueue('linker');
        }
    }
}
