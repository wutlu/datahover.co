<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\Proxy;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use Etsetra\Library\Nokogiri;
use Etsetra\Library\DateTime as DT;

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
        $base_uri = explode('/', $page)[0];

        $client = new Client([
            'base_uri' => $base_uri,
            'handler' => HandlerStack::create()
        ]);

        $params = [
            'timeout' => 15,
            'connect_timeout' => 15,
            'headers' => [
                'User-Agent' => config('crawler.user_agents')[array_rand(config('crawler.user_agents'))],
            ],
            'curl' => [
                CURLOPT_REFERER => 'https://www.google.com/search?q=site%3A'.$page.'&source=hp&uact=5',
                CURLOPT_COOKIE => 'AspxAutoDetectCookieSupport=1'
            ],
            'verify' => false,
            'allow_redirects' => [
                'max' => 10
            ]
        ];

        $proxy = Proxy::where('type', 'ipv4')->where('speed', '>', 6)->inRandomOrder()->first();

        if ($proxy)
            $params['proxy'] = "$proxy->username:$proxy->password@$proxy->ip:$proxy->port";

        try
        {
            $request = $client->get("//$page", $params);

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
                case 0: $message = 'It takes over 15 seconds to connect to this website.'; break;
                case 400: $message = '(400) Bad Request! We were unable to connect due to a technical problem on this site.'; break;
                case 401: $message = '(401) Unauthorized! This site does not accept bots.'; break;
                case 403: $message = '(403) Forbidden! This site does not allow you to access it.'; break;
                case 404: $message = '(404) Not Found! The links we found do not open.'; break;
                case 405: $message = '(405) The site does not accept our login method.'; break;
                case 407: $message = '(407) Proxy Authentication Required'; break;
                case 408: $message = '(408) Request Timeout'; break;
                case 429: $message = '(429) Too Many Requests'; break;
                case 500: $message = '(500) Internal Server Error'; break;
                case 502: $message = '(502) Bad Gateway'; break;
                case 503: $message = '(503) Service Unavailable'; break;
                case 504: $message = '(504) Gateway Timeout'; break;
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

        $saw = new Nokogiri($html);

        $links = Arr::pluck($saw->get('a[href]')->toArray(), 'href');
        $links = array_unique($links);

        foreach ($links as $link)
        {
            $append = false;

            $link = Str::beforeLast($link, '#');
            $link = Str::beforeLast($link, '?');
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
                    '\\',
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

    /**
     * @param string $html
     * @return object
     */
    public static function getSchemaInHtml(string $html)
    {
        $dom  = new \DOMDocument();
        libxml_use_internal_errors(1);
        $dom->loadHTML($html);
        $xpath = new \DOMXpath($dom);
        $jsonScripts = $xpath->query('//script[@type="application/ld+json"]');

        if ($jsonScripts->length)
        {
            for ($i = 0; $i <= 5; $i++)
            {
                $item = $jsonScripts->item($i);

                if ($item)
                {
                    $json = json_decode(trim($jsonScripts->item($i)->nodeValue));
                    
                    if (@$json->articleBody)
                        return $json;
                }
            }
        }

        return null;
    }

    /**
     * @param string $html
     * @return object
     */
    public static function getArticleInHtml(string $html)
    {
        $schema = self::getSchemaInHtml($html);

        $data = new \stdClass;
        $data->title = null;
        $data->article = null;
        $data->image = null;
        $data->created_at = null;

        if (@$schema->headline && @$schema->articleBody)
        {
            $data->title = $schema->headline;
            $data->article = $schema->articleBody;
            $data->image = @$schema->image->url ?? null;
            $data->created_at = @$schema->datePublished ? (new DT)->nowAt($schema->datePublished) : null;
        }

        if (!$data->title || !$data->article || !$data->created_at)
        {
            $saw = new Nokogiri($html);
            $metas = $saw->get('meta')->toArray();

            $title = [];
            $article = [];
            $image = [];

            if (count($metas))
            {
                foreach ($metas as $meta)
                {
                    if ((@$meta['property'] == 'og:title' || @$meta['name'] == 'twitter:title' || @$meta['name'] == 'title') && @$meta['content'])
                        $title[] = $meta['content'];
                    elseif ((@$meta['property'] == 'og:description' || @$meta['name'] == 'twitter:description' || @$meta['name'] == 'description') && @$meta['content'])
                        $article[] = $meta['content'];
                    elseif ((@$meta['property'] == 'og:image' || @$meta['name'] == 'twitter:image') && @$meta['content'])
                        $image[] = $meta['content'];
                }
            }

            if (!$data->title)
                $data->title = @$title[0] ?? ($saw->get('h1')->toText() ?? ($saw->get('title')->toText() ?? null));

            if (!$data->article)
            {
                $paragraphs = $saw->get('article p')->toText();
                $paragraphs = $paragraphs ?? (@$article[0] ?? null);

                $data->article = trim($paragraphs);
            }

            if (!$data->image)
                $data->image = @$image[0] ?? null;

            if (!$data->created_at)
            {
                preg_match('/\d{4}.\d{1,2}.\d{1,2}.\d{1,2}:\d{1,2}:\d{1,2}/', $html, $created_at);

                $data->created_at = @$created_at[0] ? (new DT)->nowAt($created_at[0]) : null;
            }
        }

        return $data;
    }
}
