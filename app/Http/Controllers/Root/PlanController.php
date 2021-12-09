<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\IdRequest;
use App\Models\Plan;

class PlanController extends Controller
{
    public function view()
    {
        return view('root.plans');
    }

    public function action(IdRequest $request)
    {
        $request->validate([
            'id' => 'nullable|integer',
            'tracks' => 'required|integer|min:0|max:10000',
            'price' => 'required|integer',
            'user_id' => 'nullable|integer|exists:users,id',
            'name' => [
                'required',
                'string',
                'max:255',
                $request->id ? "unique:plans,name,$request->id" : 'unique:plans,name'
            ],
        ]);

        $q = $request->id ? Plan::findOrFail($request->id) : new Plan;
        $q->name = $request->name;
        $q->price = $request->price;
        $q->track_limit = $request->tracks;
        $q->user_id = $request->user_id;
        $q->save();

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => $request->id ? 'Item was updated' : 'Item was created'
            ],
            'data' => $q,
            'type' => $request->id ? 'edit' : 'create'
        ];
    }

    public function read(Request $request)
    {
        $request->validate([ 'id' => 'required|integer' ]);

        return [
            'success' => 'ok',
            'data' => Plan::with([ 'user' => function($query) { $query->select('id', 'name', 'email'); } ])->findOrFail($request->id)
        ];
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ]);

        $tracks = Plan::whereIn('id', $request->id)->delete();

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

        $data = Plan::with([ 'user' => function($query) { $query->select('id', 'name', 'email'); } ])
            ->where(function($query) use($request) {
                if ($request->search)
                    $query->orWhere('user_id', $request->search);
            })
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => Plan::count()
            ]
        ];
    }
}
