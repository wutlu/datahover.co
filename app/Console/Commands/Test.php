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
        (new DataPool)->putIndexMapping(
            [
                'parent_id' => [
                    'type' => 'keyword'
                ]
            ]
        );

        exit;
        $videoList = Youtube::paginateResults([
            'q' => 'biden',
            'type' => 'video',
            'part' => 'id, snippet',
            'maxResults' => 50
        ]);

        print_r($videoList);
        exit;

        foreach ($videoList as $video)
        {
            $commentThreads = Youtube::getCommentThreadsByVideoId($video->id->videoId);

            print_r($commentThreads);
            exit;
        }
        // $site = 'timeturk.com';
        // $site = 'timeturk.com/genel/mahkemeden-osman-kavala-karari/haber-1713514';
        // $source = Crawler::getPageSource($site);
        // $collect = Crawler::getArticleInHtml($source->html);

        // print_r($collect);
    }
}
