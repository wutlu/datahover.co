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
        $site = 'timeturk.com/genel/mahkemeden-osman-kavala-karari/haber-1713514';
        $site = 'msn.com/';
        $source = Crawler::getPageSource($site);
        $collect = Crawler::getLinksInHtml($site, $source->html);

        print_r($collect);
    }
}
