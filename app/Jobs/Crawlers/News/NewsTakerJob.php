<?php

namespace App\Jobs\Crawlers\News;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Http\Controllers\Server\CrawlerController as Crawler;
use App\Http\Controllers\LogController;

use Etsetra\Elasticsearch\Console\BulkApi;
use Etsetra\Library\DateTime as DT;
use Etsetra\Library\Nokogiri;

use App\Models\DataPool;
use App\Models\Track;
use App\Models\User;

use LanguageDetection\Language;

class NewsTakerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $link;
    protected $array;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $link)
    {
        $this->link = $link;
        $this->array = [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $source = Crawler::getPageSource($this->link);

        if ($source->success == 'ok')
        {
            $article = Crawler::getArticleInHtml($source->html);

            if ($article->title && $article->article)
            {
                $this->array['status'] = 'ok';
                $this->array['title'] = $article->title;
                $this->array['text'] = $article->article;
                $this->array['lang'] = @array_keys((new Language)->detect($article->article)->close())[0] ?? 'unknown';

                if ($article->image)
                    $this->array['image'] = $article->image;
                if ($article->created_at)
                    $this->array['created_at'] = $article->created_at;
            }
            else
            {
                $this->array['status'] = 'err';
                $this->array['log'] = 'No title or text detected.';
            }
        }
        else
        {
            $site = explode('/', $this->link)[0].'/';

            $track = Track::where(
                [
                    'source' => 'news',
                    'type' => 'page',
                    'value' => $site
                ]
            )
            ->first();

            if ($track)
            {
                $track->error_hit = $track->error_hit + 1;
                $track->error_reason = $source->alert->message;
                $track->save();

                $users = User::whereIn('id', $track->users)->get();

                foreach ($users as $user)
                {
                    //LogController::create($source->alert->message, $user->id);
                }
            }

            $this->array['status'] = 'err';
            $this->array['log'] = $source->alert->message;
        }

        $update = (new DataPool)->update(md5($this->link), $this->array);

        if ($update->success != 'ok')
            throw new \Exception(json_encode($update->log));
    }
}
