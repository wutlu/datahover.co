<?php

namespace App\Jobs\Crawlers\YouTube;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Track;

use Alaouy\Youtube\Facades\Youtube;
use LanguageDetection\Language;
use Etsetra\Library\DateTime as DT;
use Etsetra\Elasticsearch\Console\BulkApi;

class YouTubeTakerJob implements ShouldQueue
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
        echo PHP_EOL;

        $this->line('Connect: '.$this->track->value);

        try
        {
            $search = Youtube::paginateResults([
                'q' => $this->track->value,
                'type' => 'video',
                'part' => 'id, snippet',
                'maxResults' => 50
            ]);
        }
        catch (\Exception $e)
        {
            $search = [];
        }

        if ($videos = @$search['results'])
        {
            foreach ($videos as $video)
            {
                $video_url = 'https://www.youtube.com/watch?v='.$video->id->videoId;
                $video_id = md5($video_url);

                BulkApi::chunk('data_pool', $video_id, [
                    'status' => 'ok',

                    'id' => $video_id,

                    'site' => 'youtube.com',
                    'link' => $video_url,
                    'title' => $video->snippet->title,
                    'text' => $video->snippet->description,
                    'lang' => @array_keys((new Language)->detect($video->snippet->description)->close())[0] ?? 'unknown',
                    'device' => 'Web',

                    'user' => [
                        'id' => @$video->snippet->channelId ?? 'unknown',
                        'title' => @$video->snippet->channelTitle ?? 'unknown',
                    ],

                    'created_at' => (new DT)->nowAt($video->snippet->publishedAt),
                    'called_at' => (new DT)->nowAt(),

                    'image' => 'https://i.ytimg.com/vi/'.$video->id->videoId.'/hqdefault.jpg',
                ], 'create');

                $this->line($video->id->videoId);

                // Take comments
                try
                {
                    $comments = Youtube::getCommentThreadsByVideoId($video->id->videoId, 100, 'time', ['id', 'replies', 'snippet'], true);
                }
                catch (\Exception $e)
                {
                    $comments = [];
                }

                if ($comments = @$comments['results'])
                {
                    foreach ($comments as $comment)
                    {
                        $snippet = @$comment->snippet->topLevelComment->snippet;

                        if ($snippet)
                        {
                            $comment_url = 'https://www.youtube.com/watch?v='.$snippet->videoId.'&lc='.$comment->id;
                            $comment_id = md5($comment_url);

                            BulkApi::chunk('data_pool', $comment_id, [
                                'status' => 'ok',

                                'id' => $comment_id,

                                'site' => 'youtube.com',
                                'link' => $comment_url,
                                'text' => $snippet->textOriginal,
                                'lang' => @array_keys((new Language)->detect($snippet->textOriginal)->close())[0] ?? 'unknown',
                                'device' => 'Web',

                                'user' => [
                                    'id' => @$snippet->authorChannelId->value ?? 'unknown',
                                    'title' => @$snippet->authorDisplayName ?? 'unknown',
                                ],

                                'created_at' => (new DT)->nowAt($snippet->publishedAt),
                                'called_at' => (new DT)->nowAt(),
                            ], 'create');

                            // $this->line($comment->id);
                        }

                        // Take replies
                        if ($replies = @$comment->replies->comments)
                        {
                            foreach ($replies as $reply)
                            {
                                $snippet = $reply->snippet;

                                $reply_url = 'https://www.youtube.com/watch?v='.$snippet->videoId.'&lc='.$reply->id;
                                $reply_id = md5($reply_url);

                                BulkApi::chunk('data_pool', $comment_id, [
                                    'status' => 'ok',

                                    'id' => $reply_id,

                                    'site' => 'youtube.com',
                                    'link' => $reply_url,
                                    'text' => $snippet->textOriginal,
                                    'lang' => @array_keys((new Language)->detect($snippet->textOriginal)->close())[0] ?? 'unknown',
                                    'device' => 'Web',

                                    'user' => [
                                        'id' => @$snippet->authorChannelId->value ?? 'unknown',
                                        'title' => @$snippet->authorDisplayName ?? 'unknown',
                                    ],

                                    'created_at' => (new DT)->nowAt($snippet->publishedAt),
                                    'called_at' => (new DT)->nowAt(),
                                ], 'create');

                                // $this->line($reply->id);
                            }
                        }
                    }
                }
            }
        }
        else
            $this->line('No videos found');

        echo PHP_EOL;
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
