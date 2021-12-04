<?php

namespace App\Jobs\Crawlers\Twitter;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

use App\Models\User;
use App\Models\TwitterToken;
use App\Models\Option;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

use Etsetra\Library\DateTime as DT;
use Etsetra\Elasticsearch\Console\BulkApi;

use App\Notifications\ServerAlert;

use App\Jobs\Crawlers\Twitter\StreamJob;

class StreamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $endpoint = "https://stream.twitter.com/1.1/";
    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = $this->client();

        try
        {
            $token = $this->token();
            $token->error_reason = null;
            $token->error_hit = 0;
            $token->status = 'working';
            $token->save();

            $response = $client->post('statuses/filter.json', [
                'form_params' => [
                    'track' => $this->token()->value
                ]
            ]);

            $stream = $response->getBody();

            $start = (new DT)->nowAt();
            $now = $start;

            while (!$stream->eof())
            {
                $obj = json_decode($this->jsonLine($stream));

                if (@$obj->id_str)
                {
                    $push = true;

                    if ($item = @$obj->retweeted_status)
                    {
                        $this->pattern($item);

                        $push = false;
                    }

                    if ($item = @$obj->quoted_status)
                        $this->pattern($item);

                    if ($push)
                        $this->pattern($obj);

                    if ($now <= (new DT)->nowAt('-10 seconds'))
                    {
                        $token = $this->token();

                        if ($token->status == 'restart' || $token->status == 'kill' || $token->status == 'close' || (new Option)->get('twitter.status', true) == 'off')
                        {
                            $this->line("Token $token->status gave");

                            if ($token->status != 'restart')
                            {
                                $token->status = 'close';
                                $token->tmp_key = null;
                                $token->value = null;
                                $token->error_hit = 0;
                                $token->error_reason = null;
                                $token->save();
                            }

                            break;
                        }

                        $token->error_reason = null;
                        $token->error_hit = 0;
                        $token->updated_at = (new DT)->nowAt();
                        $token->save();

                        $this->line('Token updated');

                        $token->save();

                        $now = (new DT)->nowAt();
                    }

                    if ($start <= (new DT)->nowAt('-60 minutes'))
                    {
                        $this->line('The token had been working for 60 minutes. Restarting to refresh.');

                        StreamJob::dispatch($this->id)->onQueue('twitter');

                        break;
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $token = $this->token();
            $token->error_reason = $e->getMessage();
            $token->error_hit = $token->error_hit + 1;
            $token->status = $token->error_hit >= 10 ? 'error' : 'run';
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

            echo $this->line($e->getMessage());
        }

    }

    /**
     * Stream Line
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
     * Tweet Pattern
     * 
     * @param object $obj
     * @return array
     */
    private function pattern(object $obj)
    {
        if ($text = @$obj->extended_tweet->full_text)
            $obj->text = $text;

        $obj->link = 'https://twitter.com/'.$obj->user->screen_name.'/status/'.$obj->id_str;

        $stdClass = [
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
            $stdClass['image'] = $item[0];

        BulkApi::chunk('data_pool', $stdClass['id'], $stdClass, 'create');

        return $stdClass;
    }

    /**
     * Generate Twitter Api Client
     * 
     * @return void
     */
    private function client()
    {
        $stack = HandlerStack::create();
        $stack->push(
            new Oauth1(
                [
                    'consumer_key' => $this->token()->consumer_key,
                    'consumer_secret' => $this->token()->consumer_secret,
                    'token' => $this->token()->access_token,
                    'token_secret' => $this->token()->access_token_secret
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
     * Echo console line
     * 
     * @param string $text
     * @return string
     */
    private function line(string $text)
    {
        echo date('H:i:s').': '.$text.PHP_EOL;
    }

    /**
     * Get real-time token
     * 
     * @param int $id
     * @return void
     */
    private function token()
    {
        return TwitterToken::find($this->id);
    }
}
