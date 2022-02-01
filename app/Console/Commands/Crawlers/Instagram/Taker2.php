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

class Taker2 extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'instagram:taker2';

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
			$blocked_ids = [];

			foreach ($tracks as $track)
			{
				$this->line("Call: $track->value");

				if ($track->subscriptionEndDate() >= date('Y-m-d'))
				{
					$this->track($track, $blocked_ids);

					if ($sleep = rand(4, 60))
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
	public function track(Track $track, array $blocked_ids = [])
	{
		$options = [];

		if ($proxy = Proxy::where('type', 'ipv4')->where('speed', '>', 4)->whereNotIn('id', $blocked_ids)->inRandomOrder()->first())
		{
			$track->request_at = (new DT)->nowAt();
			$track->request_hit = $track->request_hit + 1;

			$proxysl = "$p->username:$p->password@$p->ip:$p->port";
			$options['proxy'] = $proxysl;

			$this->info("Use proxy: $proxysl");

			$this->line("Connect hashtag: $track->value");

			try
			{
				$http = Http::withOptions($options)
					->withHeaders(
						[
							'User-Agent' => Arr::random(config('crawler.user_agents'))
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
					break;
					default:
						$message = 'Instagram ('.$e->getCode().'): '.$e->getMessage();
					break;
				}

				die($message);
			}

			if ($http->successful())
			{
				$this->info('Connected');

				$last_edges = @$http->json()['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ?? [];
				$top_edges = @$http->json()['graphql']['hashtag']['edge_hashtag_to_top_posts']['edges'] ?? [];

				$edges = array_merge($last_edges, $top_edges);

				if (count($edges))
				{
					foreach ($last_edges as $key => $edge)
					{
						$node = @$edge['node'];

						if ($node)
						{
							try
							{
								$link = 'https://www.instagram.com/p/'.$node['shortcode'].'/';
								$text = implode(PHP_EOL, Arr::flatten($node['edge_media_to_caption']['edges']));

								$row = [
									'status' => 'ok',
									'id' => md5($link),
									'site' => 'instagram.com',
									'link' => $link,
									'text' => $text,
									'lang' => $text ? (@array_keys((new Language)->detect($text)->close())[0] ?? 'unknown') : 'unknown',
									'device' => 'Mobile',
									'image' => $node['thumbnail_src'],
									'user' => [
										'id' => $node['owner']['id'],
									],
									'created_at' => (new DT)->nowAt('@'.$node['taken_at_timestamp']),
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
										"(hashtag: $track->value)"
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
						else
						{
							$track->error_hit = $track->error_hit + 1;
							$track->error_reason = 'Could not retrieve data from this hashtag. #1';
						}
					}

					$this->info(count($last_edges).' data collected');
				}
				else
				{
					$track->error_hit = $track->error_hit + 1;
					$track->error_reason = 'Could not retrieve data from this hashtag. #2';
				}
			}
			else
			{
				switch ($http->code())
				{
					case 0:
						$track->error_hit = $track->error_hit + 5;
					break;
				}

				$message = 'We got a '.$http->code().' error code from a request on the Instagram side. (hashtag: '.$track->value.')';
				$this->error($message);
			}

			if ($track->error_hit >= 25)
			{
				$message = implode(
					PHP_EOL,
					[
						"A Instagram proxy blocked due to errors."
					]
				);
				$this->error($message);

				$track->valid = false;

				Notification::send(
					User::where('is_root', true)->get(),
					(
						new ServerAlert($message)
					)
					->onQueue('notifications')
				);
			}

			$track->save();
		}
		else
		{
			$message = 'The number of proxies defined in the system is insufficient. Please increase the number.';
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
