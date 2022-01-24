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
        $http = Http::withOptions(
            [
                'proxy' => 'MyFxB6:qHMowG@45.57.180.218:8000'
            ]
        )
        ->withHeaders(
            [
                'Cookie' => 'sessionid=3540610834%3AAB8OfCPovfDu9h%3A12'
            ]
        )
        ->get('https://www.instagram.com/explore/tags/ankara/?__a=1');

        if ($http->successful())
        {
            if ($obj = @$http->json()['data'])
            {


                print_r($items);

                echo count($items);
            }
        }
    }
}
