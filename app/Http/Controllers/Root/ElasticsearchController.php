<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Etsetra\Elasticsearch\Client;

class ElasticsearchController extends Controller
{
    public function view()
    {
        return view('root.elasticsearch');
    }

    /**
     * Elasticsearch Server Status
     * 
     * @param string $status
     * @return object
     */
    public function status(string $status)
    {
        return (new Client)->cat($status);
    }
}
