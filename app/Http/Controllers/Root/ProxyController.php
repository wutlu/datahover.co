<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Proxy;
use App\Models\Option;

class ProxyController extends Controller
{
    public function view()
    {
        $options = (new Option)->get('proxy.*');

        return view('root.proxies', compact('options'));
    }

    public function list(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = Proxy::where(function($query) use($request) {
                if ($request->search)
                    $query->orWhere('ip', 'ilike', '%'.$request->search.'%');
            })
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data
        ];
    }

    public function settings(Request $request)
    {
        $keys = [
            'api_key' => 'required|string|max:255',
            'max_buy_piece' => 'required|integer|between:0,40',
            'min_balance_for_alert' => 'required|integer|between:10,100',
            'proxy_country' => 'required|string|in:ru,us,ca,de',
            'proxy_version' => 'required|integer|in:4,3,6',
            'buy_period' => 'required|integer|in:3,7,14,30,60',
        ];

        $request->validate($keys);

        foreach ($keys as $key => $rules)
        {
            (new Option)->change("proxy.$key", $request->{$key});
        }

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Proxy settings updated'
            ]
        ];
    }
}
