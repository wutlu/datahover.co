<?php

namespace App\Http\Controllers\Root\Crawlers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Option;

class NewsController extends Controller
{
    public function view()
    {
        $status = (new Option)->get('news.status', true);

        return view('root.crawlers.news', compact('status'));
    }
}
