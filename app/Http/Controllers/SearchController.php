<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\DataPool;

use Etsetra\Library\DateTime as DT;

class SearchController extends Controller
{
    public $search_rules;
    public $rate_minutes = 1;
    public $rate_limit = 960;

    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');

        $this->search_rules = [
            'search' => 'required|string|max:1000',
            'skip' => 'nullable|integer|max:1000000',
            'take' => 'nullable|integer|max:1000'
        ];
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
                            'range' => [ 'called_at' => [ 'gte' => (new DT)->nowAt('-24 hours') ] ]
                        ]
                    ]
                ]
            ],
            [
                'from' => $request->skip ?? 0,
                'size' => $take,
                'sort' => [
                    [ 'created_at' => 'desc' ]
                ]
            ]
        );

        return [
            'success' => @$query->success ?? 'failed',
            'stats' => @$query->stats ?? [ 'total' => 0 ],
            'data' => @$query->source ?? []
        ];
    }
}
