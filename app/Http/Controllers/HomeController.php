<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Etsetra\Library\DateTime as DT;

use App\Models\Logs;
use App\Models\DataPool;
use App\Models\HideInfo;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');
    }

    /**
     * Index
     * 
     * @return view
     */
    public function index(Request $request)
    {
        return view('home');
    }

    public function console()
    {
        $query = (new DataPool)->find(
            [
                'bool' => [
                    'must' => [
                        [ 'match' => [ 'status' => 'ok' ] ],
                        [
                            'query_string' => [
                                'query' => '_exists_:title',
                            ]
                        ]
                    ],
                    'filter' => [
                        [
                            'range' => [ 'created_at' => [ 'gte' => (new DT)->nowAt('-1 hours') ] ]
                        ]
                    ]
                ]
            ],
            [
                'from' => 0,
                'size' => 25,
                'sort' => [
                    [ 'created_at' => 'desc' ]
                ]
            ]
        );

        return [
            'success' => $query->success,
            'data' => $query->source
        ];
    }

    /**
     * Dashboard
     * 
     * @return view
     */
    public function dashboard()
    {
        return view('dashboard', [
            'rate_minutes' => (new LogController)->rate_minutes,
            'rate_limit' => (new LogController)->rate_limit,
            'apis' => [
                'logList' => [
                    'name' => 'Log List APi',
                    'method' => 'POST',
                    'route' => route('api.logs.list'),
                    'params' => (new LogController)->list_rules,
                ],
            ],
            'greetingWelcome' => HideInfo::where([ 'user_id' => auth()->user()->id, 'key' => 'greeting.welcome' ])->exists()
        ]);
    }

    /**
     * Portal Single Pages
     * 
     * @param string $page
     * @return view
     */
    public function page(string $page)
    {
        switch ($page)
        {
            case 'about-us':
            case 'public-offer-agreement':
            case 'privacy-policy':
                return view("pages.$page");
            break;
            default:
                return abort(404);
            break;
        }
    }
}
