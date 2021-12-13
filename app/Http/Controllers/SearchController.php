<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DataPool;

use Etsetra\Library\DateTime as DT;

class SearchController extends Controller
{
    public $search_rules;
    public $rate_minutes = 1;
    public $rate_limit = 120;

    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');

        $this->search_rules = [
            'search' => 'required|string|max:1000',
            'skip' => 'nullable|integer|max:1000000',
            'take' => 'nullable|integer|max:1000'
        ];
    }

    public function dashboard()
    {
        $apis = [
            'searchApi' => [
                'name' => 'Search APi',
                'method' => 'POST',
                'route' => route('api.search'),
                'params' => $this->search_rules,
            ],
        ];

        return view('search', [
            'rate_minutes' => $this->rate_minutes,
            'rate_limit' => $this->rate_limit,
            'apis' => $apis,
        ]);
    }

    public function searchApi(Request $request)
    {
        $request->validate($this->search_rules);

        $take = $request->take ?? 100;

        if ($request->user->subscription()->plan->price <= 0)
            $take = $take >= 10 ? 10 : $take;

        $query = (new DataPool)->find(
            [
                'bool' => [
                    'must' => [
                        [ 'match' => [ 'status' => 'ok' ] ],
                        [
                            'query_string' => [
                                'query' => $request->search,
                                'default_operator' => 'AND'
                            ]
                        ]
                    ],
                    'filter' => [
                        [
                            'range' => [ 'called_at' => [ 'gte' => (new DT)->nowAt('-1 days') ] ]
                        ]
                    ]
                ]
            ],
            [
                'from' => $request->skip ?? 0,
                'size' => $take,
                'sort' => [
                    [ 'called_at' => 'desc' ]
                ]
            ]
        );

        return [
            'success' => @$query->success ?? 'failed',
            'data' => @$query->source ?? [],
            'stats' => @$query->stats ?? [ 'total' => 0 ]
        ];
    }
}
