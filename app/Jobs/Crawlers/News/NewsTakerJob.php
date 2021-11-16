<?php

namespace App\Jobs\Crawlers\News;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Controllers\Server\CrawlerController as Crawler;
use Etsetra\Elasticsearch\Console\BulkApi;
use Etsetra\Library\DateTime as DT;

class NewsTakerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $link;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //$source = Crawler::getPageSource($this->link);

        //print_r($source);
    }
}
