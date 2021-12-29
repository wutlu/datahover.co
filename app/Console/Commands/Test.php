<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Server\CrawlerController as Crawler;
use Alaouy\Youtube\Facades\Youtube;
use App\Models\DataPool;
use Illuminate\Support\Str;

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
        $site = 'foxnews.com';
        $article = 'foxnews.com/politics/ron-klain-bidens-chief-of-staff-retweets-column-calling-2021-not-all-bad';

        $source = (new Crawler)->getPageSource($article);
        //$links = (new Crawler)->getLinksInHtml($site, $source->html);
        $article = (new Crawler)->getArticleInHtml($source->html);

        print_r($article);
    }
}
