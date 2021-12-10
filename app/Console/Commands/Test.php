<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Server\CrawlerController as Crawler;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $site = 'theguardian.com/sport/live/2021/dec/10/ashes-2021-22-day-3-three-cricket-australia-vs-england-first-test-live-score-card-aus-v-eng-start-time-latest-updates';
        $source = Crawler::getPageSource($site);
        $collect = Crawler::getArticleInHtml($source->html);

        print_r($collect);
    }
}
