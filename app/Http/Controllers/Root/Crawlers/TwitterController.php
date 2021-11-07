<?php

namespace App\Http\Controllers\Root\Crawlers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\TwitterToken;
use App\Models\Option;

use App\Services\Twitter\TokenGenerator;

class TwitterController extends Controller
{
    public function view()
    {
        $status = (new Option)->get('twitter.status', true);

        return view('root.crawlers.twitter', compact('status'));
    }

    public function tokens(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = TwitterToken::where(function($query) use($request) {
                if ($request->search)
                    $query->orWhere('screen_name', 'ilike', '%'.$request->search.'%');
            })
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => TwitterToken::count()
            ]
        ];
    }

    public function createToken(Request $request)
    {
        $request->validate(
            [
                'screen_name' => 'required|string|max:100',
                'password' => 'required|string|max:100',
            ]
        );

        $messages = [];

        /**
         * Girilen hesap üzerinden Twitter official api ile token üretir.
         */
        foreach (config('services.twitter.consumer_apis') as $device => $item)
        {
            $connection = new TokenGenerator($item['consumer_key'], $item['consumer_secret']);

            $access = $connection->getXAuthToken($request->screen_name, $request->password);

            if (@$access['oauth_token'] && @$access['oauth_token_secret'])
            {
                $new = TwitterToken::updateOrCreate(
                    [
                        'consumer_key' => $item['consumer_key'],
                        'access_token' => $access['oauth_token'],
                    ],
                    [
                        'device' => $device,
                        'screen_name' => $request->screen_name,
                        'password' => $request->password,
                        'consumer_key' => $item['consumer_key'],
                        'consumer_secret' => $item['consumer_secret'],
                        'access_token' => $access['oauth_token'],
                        'access_token_secret' => $access['oauth_token_secret'],
                    ]
                );

                $messages[] = "$device key added";
            }
            else
                $messages[] = "Failed to add $device key";
        }

        return [
            'success' => 'ok',
            'alert' => [
            	'type' => 'default',
            	'message' => implode('<br />', $messages)
            ]
        ];
    }

    public function deleteToken(Request $request)
    {
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ]);

        $tracks = TwitterToken::whereIn('id', $request->id)->delete();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Items was deleted'
            ]
        ];
    }
}
