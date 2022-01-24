<?php

namespace App\Console\Commands\Crawlers\Instagram;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

use App\Models\User;
use App\Models\Track;
use App\Models\Proxy;
use App\Models\InstagramAccount;

use Etsetra\Library\DateTime as DT;
use Etsetra\Elasticsearch\Console\BulkApi;
use DB;
use LanguageDetection\Language;
use App\Notifications\ServerAlert;

class Taker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instagram:taker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiates Instagram tracking.';

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
            ->where('source', 'instagram')
            ->where('type', 'hashtag')
            ->where('request_at', '<=', DB::raw("NOW() - INTERVAL '1 minutes' * request_frequency"))
            ->orderBy('request_at', 'asc')
            ->get();

        if (count($tracks))
        {
            foreach ($tracks as $track)
            {
                $this->info("Call: $track->value");

                if ($track->subscriptionEndDate() >= date('Y-m-d'))
                {
                    $track->request_at = (new DT)->nowAt();
                    $track->request_hit = $track->request_hit + 1;
                    $track->valid = true;
                    $track->save();

                	$this->track($track);

                    if ($sleep = rand(5, 10))
                    {
                    	$this->info("Wait $sleep sec");
                    	sleep($sleep);
                	}
                }
                else
                    $this->error('This track does not belong to a current account.');
            } 
        }
        else
            $this->info('The track to be tracked could not be found.');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function track(Track $track)
    {
        $account = InstagramAccount::where('status', 'normal')
	        ->orderBy('request_at', 'asc')
	        ->first();

        if ($account)
        {
        	$account->request_at = (new DT)->nowAt();
        	$account->request_hit = $account->request_hit + 1;

        	// set proxy
        	if (!$account->proxy)
        	{
        		$p = Proxy::where('type', 'ipv4')->where('speed', '>', 4)->inRandomOrder()->first();

        		if ($p)
        		{
        			$account->proxy = "$p->username:$p->password@$p->ip:$p->port";
        		}
        	}

        	// set user-agent
        	if (!$account->user_agent)
        		$account->user_agent = Arr::random(config('crawler.user_agents'));

	        $this->line('Connect: '.$track->value);

	        $options = [];

	        if ($proxy = $account->proxy)
	        	$options['proxy'] = $proxy;

	        try
	        {
		        $http = Http::withOptions($options)
			        ->withHeaders(
			            [
			                'Cookie' => "sessionid=$account->sessionid",
			                'User-Agent' => $track->user_agent
			            ]
			        )
			        ->get("https://www.instagram.com/explore/tags/$track->value/?__a=1");
		    }
		    catch (\Exception $e)
		    {
		    	switch ($e->getCode())
		    	{
		    		case 0:
		    			$message = 'Failed to connect to Instagram. The proxy server of this Instagram account has been wasted.';

		    			$account->proxy = null;
		    		break;
		    		default:
		    			$message = 'Instagram ('.$e->getCode().'): '.$e->getMessage();
		    		break;
		    	}

		    	$account->save();

		    	die($message);
		    }

		    if ($http->successful())
		    {
		    	$this->info('Connected');

		    	if ($obj = @$http->json()['data'])
		    	{
		    		$this->info(count($obj).' data found');

		    		##
		    		#
		    		# DATA FETCH
		    		#
		    		##
			        $data = array_merge($obj['recent']['sections'], $obj['top']['sections']);
			        $data = array_map(function($item) {
			            return $item['layout_content']['medias'];
			        }, $data);

			        foreach ($data as $medias)
			        {
			            foreach ($medias as $item)
			            {
			                try
			                {
			                    $link = 'https://www.instagram.com/p/'.$item['media']['code'].'/';
			                    $text = $item['media']['caption']['text'];

			                    $row = [
			                        'status' => 'ok',
			                        'id' => md5($link),
			                        'site' => 'instagram.com',
			                        'link' => $link,
			                        'text' => $text,
			                        'lang' => @array_keys((new Language)->detect($text)->close())[0] ?? 'unknown',
			                        'device' => 'Mobile',
			                        'image' => @$item['media']['carousel_media'][0]['image_versions2']['candidates'][0]['url'] ?? $item['media']['image_versions2']['candidates'][0]['url'],
			                        'user' => [
			                            'id' => $item['media']['caption']['user']['pk'],
			                            'name' => $item['media']['caption']['user']['username'],
			                            'title' => $item['media']['caption']['user']['full_name'],
			                            'image' => $item['media']['caption']['user']['profile_pic_url'],
			                        ],
			                        'created_at' => (new DT)->nowAt('@'.$item['media']['caption']['created_at_utc']),
			                        'called_at' => (new DT)->nowAt(),
			                    ];

			                    BulkApi::chunk('data_pool', $row['id'], $row, 'create');
			                }
			                catch (\Exception $e)
			                {
			                	$message = implode(
			                		PHP_EOL,
			                		[
			                			'Check the Instagram post pattern.',
			                			$e->getMessage(),
			                			"(hashtag: $track->value, account: $account->email)"
			                		]
			                	);
				                $this->error($message);

				                Notification::send(
				                    User::where('is_root', true)->get(),
				                    (
				                    	new ServerAlert($message)
				                    )
				                    ->onQueue('notifications')
				                );
			                }

			                $all_comments = [];

			                if ($comments = @$item['media']['preview_comments'])
			                	$all_comments = array_merge($all_comments, $comments);

			                if ($comments = @$item['media']['comments'])
			                	$all_comments = array_merge($all_comments, $comments);

		    				$this->info(count($all_comments).' comment found');

		                    foreach ($all_comments as $comment)
		                    {
		                        $link = 'https://www.instagram.com/p/'.$item['media']['code'].'/#comment_id:'.$comment['pk'];
		                        $text = $comment['text'];

		                        try
		                        {
			                        $row = [
			                            'status' => 'ok',
			                            'id' => md5($link),
			                            'site' => 'instagram.com',
			                            'link' => $link,
			                            'text' => $text,
			                            'lang' => @array_keys((new Language)->detect($text)->close())[0] ?? 'unknown',
			                            'device' => 'Mobile',
			                            'user' => [
			                                'id' => $comment['user']['pk'],
			                                'name' => $comment['user']['username'],
			                                'title' => $comment['user']['full_name'],
			                                'image' => $comment['user']['profile_pic_url'],
			                            ],
			                            'created_at' => (new DT)->nowAt('@'.$comment['created_at_utc']),
			                            'called_at' => (new DT)->nowAt(),
			                        ];

			                    	BulkApi::chunk('data_pool', $row['id'], $row, 'create');
			                    }
				                catch (\Exception $e)
				                {
				                	$message = implode(
				                		PHP_EOL,
				                		[
				                			'Check the Instagram comment pattern.',
				                			$e->getMessage(),
				                			"(hashtag: $track->value, account: $account->email)"
				                		]
				                	);
				                	$this->error($message);

					                Notification::send(
					                    User::where('is_root', true)->get(),
					                    (
					                    	new ServerAlert($message)
					                    )
					                    ->onQueue('notifications')
					                );
				                }
		                    }
			            }
			        }
		    		##
		    		#
		    		# /DATA FETCH
		    		#
		    		##
		    	}
		    	else
		    	{
		    		$message = "No data came from a request made by Instagram. (hashtag: $track->value)";
		    		$this->error($message);
		    		$account->error_hit = $account->error_hit + 2;
	    			$account->error_reason = $message;
		    	}
		    }
		    else
		    {
		    	switch ($http->code())
		    	{
		    		case 0:
		    		break;
		    	}

		    	$message = 'We got a '.$http->code().' error code from a request on the Instagram side. (hashtag: '.$track->value.', account: '.$account->email.')';
		    	$this->error($message);

	    		$account->error_hit = $account->error_hit + 1;
	    		$account->error_reason = $message;
		    }

		    if ($account->error_hit >= 10)
		    {
		    	$message = implode(
        			PHP_EOL,
        			[
        				"A Instagram Bot closed due to errors. ($account->email)",
        				$account->error_reason
        			]
        		);
		    	$this->error($message);

		    	$account->status = 'error';

                Notification::send(
                    User::where('is_root', true)->get(),
                    (
                    	new ServerAlert($message)
                    )
                    ->onQueue('notifications')
                );
		    }

		    $account->save();
        }
        else
        {
        	$message = 'The number of Instagram accounts defined in the system is insufficient. Please increase the number.';
        	$this->error($message);

            Notification::send(
                User::where('is_root', true)->get(),
                (
                	new ServerAlert($message)
                )
                ->onQueue('notifications')
            );
        }

        $this->newLine();
    }
}
