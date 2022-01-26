<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Server\CrawlerController as Crawler;
use Alaouy\Youtube\Facades\Youtube;
use App\Models\DataPool;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use LanguageDetection\Language;
use Etsetra\Library\DateTime as DT;
use App\Http\Controllers\SearchController;

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
        $q = (new SearchController)->save('site:haberturk.com OR site:instagram.com', 'test');

        print_r($q);
    }
}
