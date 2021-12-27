<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Server\CrawlerController as Crawler;
use Alaouy\Youtube\Facades\Youtube;
use App\Models\DataPool;

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
        $site = 'hurriyet.com.tr/';
        $link = 'foxnews.com/us/1619-project-founder-says-shes-not-a-professional-educator-despite-being-college-faculty-member';

        $source = (new Crawler)->getPageSource($link);
        //$links = (new Crawler)->getLinksInHtml($site, $source->html);

        print_r($source);
        exit;

        $news = (new Crawler)->getArticleInHtml($source->html);
        print_r($news);
    }
}
