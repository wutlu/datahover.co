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
        $site = 'timeturk.com/spor/brezilyali-pele-tekrar-hastanede-pele-nin-hastaligi-ne-pele-kanser-mi/haber-1713293';
        $source = Crawler::getPageSource($site);
        $collect = Crawler::getArticleInHtml($source->html);

        print_r($collect);
    }
}
