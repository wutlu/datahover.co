<?php

namespace App\Http\Controllers\Root\Crawlers;

use App\Http\Controllers\Controller;

use App\Models\Option;

class YouTubeController extends Controller
{
    public function view()
    {
        $status = (new Option)->get('youtube.status', true);

        return view('root.crawlers.youtube', compact('status'));
    }
}
