<?php

namespace App\Jobs\Crawlers\News;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Track;

use App\Http\Controllers\Server\CrawlerController as Crawler;
use Etsetra\Elasticsearch\Console\BulkApi;
use Etsetra\Library\DateTime as DT;

class LinkDetectorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $track;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Track $track)
    {
        $this->track = $track;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $source = Crawler::getPageSource($this->track->value);

        if ($source->success == 'ok')
        {
            $this->track->error_hit = 0;
            $this->track->error_reason = null;

            $collect = Crawler::getLinksInHtml($this->track->value, $source->html);

            if ($collect->success == 'ok')
            {
                foreach ($collect->links as $link)
                {
                    $id = md5($link);

                    BulkApi::chunk(
                        'data_pool',
                        $id,
                        [
                            'id' => $id,
                            'site' => strtolower(explode('/', $link)[0]),
                            'link' => $link,
                            'device' => 'Web',
                            'status' => 'call',
                            'created_at' => (new DT)->nowAt()
                        ],
                        'create'
                    );
                }

                $this->line('The collection has been submitted to Redis.');
            }
            else
            {
                $this->track->error_hit = $this->track->error_hit + 1;
                $this->track->error_reason = 'Could not detect link on home page.';
                $this->line($this->track->error_reason);
            }
        }
        else
        {
            $this->track->error_hit = $this->track->error_hit + 1;
            $this->track->error_reason = $source->alert->message;
            $this->line($this->track->error_reason);
        }

        if ($this->track->error_hit >= 20)
        {
            $this->track->valid = false;
            $this->line('Track closed');
        }

        $this->track->save();
    }

    /**
     * Echo console line
     * 
     * @param string $text
     * @return string
     */
    private function line(string $text)
    {
        echo date('H:i:s').': '.$text.PHP_EOL;
    }
}
