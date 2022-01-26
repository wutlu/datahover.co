<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\DataPool;

use Etsetra\Library\DateTime as DT;
use Etsetra\Library\ArrayTo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Rap2hpoutre\FastExcel\FastExcel;

class SearchController extends Controller
{
    public $search_rules;
    public $rate_minutes = 1;
    public $rate_limit = 960;

    public function __construct()
    {
        $this->middleware('auth')->only([ 'view' ]);

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
                    'filter' => [ [ 'range' => [ 'called_at' => [ 'gte' => (new DT)->nowAt('-24 hours') ] ] ] ]
                ]
            ],
            [
                'from' => $request->skip ?? 0,
                'size' => $take,
                'sort' => [ [ 'created_at' => 'desc' ] ]
            ]
        );

        return [
            'success' => @$query->success ?? 'failed',
            'stats' => @$query->stats ?? [ 'total' => 0 ],
            'data' => @$query->source ?? []
        ];
    }

    /**
     * Search View
     * 
     * @return view
     */
    public function view()
    {
        $sc = new SearchController;

        $apis = [
            'searchApi' => [
                'name' => 'Search APi',
                'method' => 'POST',
                'route' => route('api.search'),
                'params' => $sc->search_rules,
            ],
        ];

        return view('search', [
            'rate_minutes' => $sc->rate_minutes,
            'rate_limit' => $sc->rate_limit,
            'apis' => $apis,
        ]);
    }

    /**
     * Search and save file
     * 
     * @param string $query
     * @param string
     * @return object
     */
    public function saveFeeds(string $query, string $key)
    {
        $query = (new DataPool)->find(
            [
                'bool' => [
                    'must' => [
                        [ 'match' => [ 'status' => 'ok' ] ],
                        [
                            'query_string' => [
                                'query' => $query,
                                'default_operator' => 'AND'
                            ]
                        ]
                    ],
                    'filter' => [ [ 'range' => [ 'called_at' => [ 'gte' => (new DT)->nowAt('-24 hours') ] ] ] ]
                ]
            ],
            [
                'from' => 0,
                'size' => 1000,
                'sort' => [ [ 'created_at' => 'desc' ] ]
            ]
        );

        if (@$query->success == 'ok' && @$query->stats['total'])
        {
            Storage::disk('feeds')->put("$key/file.xml", (new ArrayTo)->xml($query->source));
            Storage::disk('feeds')->put("$key/file.json", json_encode($query->source, JSON_PRETTY_PRINT));

            return [
                'success' => 'ok',
                'data' => [
                    'xml' => Storage::disk('feeds')->url("$key/file.xml"),
                    'json' => Storage::disk('feeds')->url("$key/file.json"),
                ],
                'stats' => $query->stats
            ];
        }
        else
            return [
                'success' => 'failed',
                'alert' => [
                    'type' => 'warning',
                    'message' => 'No data was found as a result of the query. This query will be run at regular intervals. Results will be updated when data is found.'
                ]
            ];
    }
}
