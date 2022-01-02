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
        $site = 'economist.com/science-and-technology/2022/01/01/omicron-causes-a-less-severe-illness-than-earlier-variants';

        $source = (new Crawler)->getPageSource($site);
        // $xxx = (new Crawler)->getLinksInHtml($site, $source->html);
        $xxx = (new Crawler)->getArticleInHtml($source->html);

        print_r($xxx);
    }
}
