<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use nokogiri;

class CrawlerController extends Controller
{
	/**
	 * Get entered page dom source
	 * 
	 * @param string $page
	 * @return object
	 */
    public static function getPageSource(string $page)
    {
        $client = new Client([
            'base_uri' => "//$page",
            'handler' => HandlerStack::create()
        ]);

        $params = [
            'timeout' => 10,
            'connect_timeout' => 10,
            'headers' => [
                'User-Agent' => config('crawler.user_agents')[array_rand(config('crawler.user_agents'))]
            ],
            'curl' => [
                CURLOPT_REFERER => 'https://www.google.com/search?q=site%3A'.$page.'&source=hp&uact=5',
                CURLOPT_COOKIE => 'AspxAutoDetectCookieSupport=1'
            ],
            'verify' => false,
            'allow_redirects' => [
                'max' => 6
            ]
        ];

        $proxy = Proxy::where('type', 'ipv4')->where('speed', '>', 6)->inRandomOrder()->first();

        if ($proxy)
            $params['proxy'] = "$proxy->username:$proxy->password@$proxy->ip:$proxy->port";

        try
        {
            $request = $client->get('/', $params);

            return (object) [
                'success' => 'ok',
                'html' => "".$request->getBody().""
            ];
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();

            switch ($e->getCode())
            {
                case 0: $message = 'It takes over 10 seconds to connect to this website.'; break;
                case 502: $message = 'This resource did not provide a valid response to our connection requests.'; break;
            }

            return (object) [
                'success' => 'failed',
                'alert' => (object) [
                    'type' => 'danger',
                    'message' => $message
                ],
                'code' => $e->getCode()
            ];
        }
    }

    /**
     * Get links in page source
     * 
     * @param string $site
     * @param string $html
     * @return object
     */
    public static function getLinksInHtml(string $site, string $html)
    {
        $chunks = [];

        $saw = new \nokogiri($html);

        $links = Arr::pluck($saw->get('a[href]')->toArray(), 'href');
        $links = array_unique($links);

        foreach ($links as $link)
        {
            $append = false;

            $link = Str::beforeLast($link, '#');
            $link_without_protocol = str_replace([ 'https://', 'http://', 'www.' ], '', $link);
            $clean_link = str_replace('//', '/', Str::start($link_without_protocol, $site));
            $clean_link_ending_without_slash = Str::replaceLast('/', '', str_replace('//', '/', Str::start($clean_link, $site)));
            $segments = explode('/', $clean_link_ending_without_slash);
            $slug_length = strlen(str_replace($site, '', $clean_link));

            $check_local = Str::startsWith($link, 'http') ? Str::startsWith($link_without_protocol, $site) : true;
            $check_length = $slug_length >= 20;
            $check_not_contains = !Str::contains(
                $clean_link,
                [
                    '?',
                    '#',
                    '/javascript',
                    '/cdn',
                    '/Object',
                    '/ ',
                    '//',
                ]
            );
            $check_contains = count($segments) == 2 ? Str::contains(
                $clean_link,
                [
                    '-',
                    '_',
                ]
            ) : true;
            $check_segment = count($segments) == 2 ? strlen($segments[1]) >= 24 : true;

            if ($check_local && $check_length && $check_not_contains && $check_contains && $check_segment)
                $chunks[preg_replace('/(-+)/', '-', preg_replace('/[a-z0-9.]+/i', '-', $clean_link))][] = $clean_link;
        }

        $collect = [
            'alloweds' => [],
            'deleteds' => [],
        ];

        foreach ($chunks as $chunk)
        {
            if (count($chunk) >= 8)
                $collect['alloweds'][] = $chunk;
            else
                $collect['deleteds'][] = $chunk;
        }

        $collect = Arr::flatten($collect['alloweds']);
        $collect = array_unique($collect);
        $collect = array_values($collect);

        return (object) [
            'success' => 'ok',
            'links' => $collect
        ];
    }
}
