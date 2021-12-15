<?php

namespace App\Console\Commands\Crawlers\Twitter;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use App\Models\TwitterToken;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Etsetra\Library\DateTime as DT;
use Etsetra\Elasticsearch\Console\BulkApi;

use App\Notifications\ServerAlert;
use App\Models\User;

class Stream extends Command
{
    protected $endpoint = "https://stream.twitter.com/1.1/";
    protected $start_at;
    protected $now_at;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:stream {--token_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Twitter stream process';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->start_at = (new DT)->nowAt();
        $this->now_at = $this->start_at;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token_id = $this->option('token_id');

        if (!$token_id)
        {
            $names = [ 'id', 'screen_name', 'device', 'status', 'value' ];

            $this->table(
                $names,
                TwitterToken::select($names)->get()->toArray()
            );

            $token_id = $this->ask('Enter a Twitter token id');
        }

        $token = $this->token(intval($token_id));

        if ($token)
        {
            $this->info((new DT)->nowAt().' Token found: '.$token->id);

            /**
             * PROCESS
             */

            $client = $this->client($token);

            try
            {
                if (($token->status == 'start' || $token->status == 'restart') && $token->value)
                {
                    $this->info((new DT)->nowAt().' Token running: '.$token->id);

                    $response = $client->post('statuses/filter.json', [
                        'form_params' => [
                            'track' => $token->value
                        ]
                    ]);

                    $token->error_reason = null;
                    $token->error_hit = 0;
                    $token->status = 'working';
                    $token->pid = getmypid();
                    $token->save();

                    $stream = $response->getBody();

                    while (!$stream->eof())
                    {
                        $obj = json_decode($this->jsonLine($stream));

                        if (@$obj->id_str)
                        {
                            $push = true;

                            if ($item = @$obj->retweeted_status)
                            {
                                $this->item($item);

                                $push = false;
                            }

                            if ($item = @$obj->quoted_status)
                                $this->item($item);

                            if ($push)
                                $this->item($obj);

                            if ($this->now_at <= (new DT)->nowAt('-10 seconds'))
                            {
                                $token = $this->token($token->id);

                                $token->updated_at = (new DT)->nowAt();
                                $token->pid = getmypid();
                                $token->save();

                                $this->info((new DT)->nowAt().' Token updated: '.$token->id);

                                $this->now_at = (new DT)->nowAt();
                            }

                            if ($this->start_at <= (new DT)->nowAt('-60 minutes'))
                            {
                                $this->line('The token had been working for 60 minutes. Restarting to refresh: '.$token->id);

                                break;
                            }
                        }
                    }
                }
                else
                    $this->error('Token not organized: '.$token->id);
            }
            catch (\Exception $e)
            {
                $token = $this->token($token->id);
                $token->error_reason = $e->getMessage();
                $token->error_hit = $token->error_hit + 1;
                $token->status = $token->error_hit >= 10 ? 'error' : 'start';
                $token->save();

                $message = [
                    'Twitter token ('.$token->screen_name.') stalled '.$token->error_hit.' times.',
                    'The error message is as follows: '.$e->getMessage(),
                    '('.$token->value.')'
                ];

                if ($token->status == 'error')
                    $message[] = 'Token closed!';

                Notification::send(
                    User::where('is_root', true)->get(),
                    (new ServerAlert(implode(' ', $message)))->onQueue('notifications')
                );

                $this->error($e->getMessage());
            }

            /**
             ****
               **/
        }
        else
            $this->error('Token not found');
    }

    /**
     * Get a token
     * 
     * @param int $id
     * @return \App\Models\TwitterToken
     */
    private function token(int $id)
    {
        return TwitterToken::find($id);
    }

    /**
     * Generate Twitter api client
     * 
     * @param \App\Models\TwitterToken
     * @return \GuzzleHttp\Client
     */
    private function client(TwitterToken $token)
    {
        $stack = HandlerStack::create();
        $stack->push(
            new Oauth1(
                [
                    'consumer_key' => $token->consumer_key,
                    'consumer_secret' => $token->consumer_secret,
                    'token' => $token->access_token,
                    'token_secret' => $token->access_token_secret
                ]
            )
        );

        return new Client(
            [
                'base_uri' => $this->endpoint,
                'handler' => $stack,
                'auth' => 'oauth',
                'stream' => true
            ]
        );
    }

    /**
     * Stream line
     * 
     * @param mixed $stream
     * @param string $buffer
     * @param int $size
     * @return object
     */
    private function jsonLine($stream, string $buffer = '', int $size = 0)
    {
        while (!$stream->eof())
        {
            if (false === ($byte = $stream->read(1)))
                return $buffer;

            $buffer .= $byte;

            if (++$size == null || substr($buffer, -strlen(PHP_EOL)) === PHP_EOL)
                break;
        }

        return $buffer;
    }

    /**
     * Get Tweet
     * 
     * @param object $obj
     * @return array
     */
    private function item(object $obj)
    {
        if ($text = @$obj->extended_tweet->full_text)
            $obj->text = $text;

        $obj->link = 'https://twitter.com/'.$obj->user->screen_name.'/status/'.$obj->id_str;

        $array = [
            'status' => 'ok',

            'id' => md5($obj->link),

            'site' => 'twitter.com',
            'link' => $obj->link,
            'text' => $obj->text,
            'lang' => $obj->lang,
            'device' => strip_tags($obj->source),

            'user' => [
                'id' => $obj->user->id_str,
                'name' => $obj->user->screen_name,
                'title' => $obj->user->name,
                'image' => str_replace('_normal', '', $obj->user->profile_image_url),
                'description' => $obj->user->description,
            ],

            'created_at' => (new DT)->nowAt($obj->created_at),
            'called_at' => (new DT)->nowAt(),
        ];

        if ($item = @$obj->extended_entities->media)
            $array['image'] = $item[0];

        BulkApi::chunk('data_pool', $array['id'], $array, 'create');

        return $array;
    }
}
