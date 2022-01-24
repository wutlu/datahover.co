<?php

namespace App\Console\Commands\Crawlers\YouTube;

use Illuminate\Console\Command;

use App\Models\Track;
use Etsetra\Library\DateTime as DT;
use Etsetra\Elasticsearch\Console\BulkApi;
use DB;

use Alaouy\Youtube\Facades\Youtube;
use LanguageDetection\Language;

class Taker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:taker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiates YouTube tracking.';

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
        $tracks = Track::where(function($query) {
                $query->orWhere('valid', true);
                $query->orWhereNull('valid');
            })
            ->where('source', 'youtube')
            ->where('type', 'keyword')
            ->where('request_at', '<=', DB::raw("NOW() - INTERVAL '1 minutes' * request_frequency"))
            ->orderBy('id', 'asc')
            ->get();

        if (count($tracks))
        {
            foreach ($tracks as $track)
            {
                $this->info("Call: $track->value");

                if ($track->subscriptionEndDate() >= date('Y-m-d'))
                {
                    $this->track($track);

                    $track->request_at = (new DT)->nowAt();
                    $track->request_hit = $track->request_hit + 1;
                    $track->valid = true;
                    $track->save();
                }
                else
                    $this->error('This track does not belong to a current account.');
            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function track(Track $track)
    {
        $this->newLine();

        $this->line('Connect: '.$track->value);

        try
        {
            $search = Youtube::paginateResults([
                'q' => $track->value,
                'type' => 'video',
                'part' => 'id, snippet',
                'maxResults' => 50
            ]);
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
            exit;
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

        $this->newLine();
    }
}
