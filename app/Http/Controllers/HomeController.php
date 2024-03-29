<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

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
        $plans = Plan::whereIn('name', [ 'Basic', 'Enterprise', 'Company' ])->orderBy('id', 'asc')->get();

        return view('home', compact('plans'));
    }

    /**
     * Dashboard
     * 
     * @return view
     */
    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Example Search
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function search(Request $request)
    {
        $response = Http::withHeaders(
            [
                'X-Api-Key' => config('services.datahover.api_key'),
                'X-Api-Secret' => config('services.datahover.api_secret'),
                'Accept' => 'application/json'
            ]
        )
        ->post(config('services.datahover.base_uri').'/search', [
            'search' => 'biden',
            'take' => 100
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
            case 'api-guide':
                if (@config('sources')[$name])
                    return view("pages.api_guides.$name", [
                        'rate_minutes' => (new TrackController)->rate_minutes,
                        'rate_limit' => (new TrackController)->rate_limit,
                        'apis' => array_merge((new TrackController)->apis, (new SearchController)->apis),
                    ]);
                else
                    return abort(404);
            break;
        }
    }
}
