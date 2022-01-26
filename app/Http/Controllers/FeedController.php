<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\IdRequest;
use App\Models\Feed;

class FeedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except([ 'feed' ]);
    }

    public function view()
    {
        return view('feeds');
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required_without:id|string|max:64',
            'search' => 'required_without:id|string|max:512'
        ]);

        $q = new Feed;
    	$q->name = $request->name;
    	$q->query = $request->search;
    	$q->key = Str::random(2).$request->user()->id.Str::random(2).date('ymdhis').Str::random(2);
        $q->user_id = $request->user()->id;
        $q->save();

        return (new SearchController)->saveFeeds($request->search, $q->key);
    }

    public function read(Request $request)
    {
        $request->validate([ 'id' => 'required|integer' ]);

        return [
            'success' => 'ok',
            'data' => Feed::where('user_id', $request->user()->id)->findOrFail($request->id)
        ];
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ]);

        $items = Feed::whereIn('id', $request->id)->get();

        foreach ($items as $item)
        {
            Storage::disk('feeds')->deleteDirectory($item->key);

            $item->delete();
        }

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Items was deleted'
            ]
        ];
    }

    public function list(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = Feed::where('user_id', $request->user()->id)
            ->where('name', 'ILIKE', '%'.$request->search.'%')
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => Feed::count()
            ]
        ];
    }
}
