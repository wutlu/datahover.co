<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

use Etsetra\Library\DateTime as DT;

use App\Models\Logs;
use App\Models\DataPool;
use App\Models\HideInfo;
use App\Models\Plan;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('dashboard');
        $this->middleware('\GrahamCampbell\Throttle\Http\Middleware\ThrottleMiddleware:30,1')->only('search');
    }

    /**
     * Index
     * 
     * @return view
     */
    public function index()
    {
        $plans = Plan::whereIn('name', [ 'Basic', 'Enterprise', 'Company' ])->get();

        return view('home', compact('plans'));
    }

    /**
     * Dashboard
     * 
     * @return view
     */
    public function dashboard()
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

        return view('dashboard', [
            'rate_minutes' => $sc->rate_minutes,
            'rate_limit' => $sc->rate_limit,
            'apis' => $apis,
        ]);
    }

    /**
     * Example Search
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function search(Request $request)
    {
        $request->validate(
            [
                'search' => 'required|string|max:64'
            ]
        );

        $response = Http::withHeaders(
            [
                'X-Api-Key' => config('services.datahover.api_key'),
                'X-Api-Secret' => config('services.datahover.api_secret'),
                'Accept' => 'application/json'
            ]
        )
        ->post(config('services.datahover.base_uri').'/search', [
            'search' => "(site:foxnews.com OR site:nytimes.com) ($request->search)",
            'take' => 9
        ]);

        return $response->body();
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

                return abort(404);
            break;
            case 'page':
                switch ($name)
                {
                    case 'about-us':
                        return view("pages.$name");
                    break;
                }

                return abort(404);
            break;
        }
    }
}
