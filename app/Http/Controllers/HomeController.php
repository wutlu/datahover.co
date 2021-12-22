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
        return view('dashboard', [
            'greetingWelcome' => HideInfo::where([ 'user_id' => auth()->user()->id, 'key' => 'greeting.welcome' ])->exists()
        ]);
    }

    public function console()
    {
        $response = Http::withHeaders(
            [
                'X-Api-Key' => config('services.datahover.api_key'),
                'X-Secret-Key' => config('services.datahover.secret_key'),
                'Accept' => 'application/json'
            ]
        )
        ->post(config('services.datahover.base_uri').'/search', [
            'search' => '_exists_:title'
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
