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
            'success' => @$query->success ?? 'failed',
            'data' => @$query->source
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
            'greetingWelcome' => HideInfo::where([ 'user_id' => auth()->user()->id, 'key' => 'greeting.welcome' ])->exists()
        ]);
    }

    /**
     * Portal Single Pages
     * 
     * @param string $name
     * @return view
     */
    public function page(string $base, string $name)
    {
        switch ($base)
        {
            case 'legal':
                switch ($name)
                {
                    case 'privacy-policy':
                    case 'terms-of-service':
                        return view("pages.$name");
                    break;
                }
            break;
            case 'page':
                switch ($name)
                {
                    case 'about-us':
                        return view("pages.$name");
                    break;
                }
            break;
        }

        return abort(404);
    }
}
