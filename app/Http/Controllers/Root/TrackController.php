<?php

namespace App\Http\Controllers\Root;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Etsetra\Library\DateTime as DT;

use Illuminate\Http\Request;

class TrackController extends Controller
{
    public function view()
    {
        return view('root.tracks');
    }

    public function read(Request $request)
    {
        $request->validate([ 'id' => 'required|integer' ]);

        return [
            'success' => 'ok',
            'data' => Track::findOrFail($request->id)
        ];
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'reason' => 'nullable|string|max:100',
        ]);

        $data = Track::findOrFail($request->id);
        $data->error_reason = $request->reason;
        $data->valid = $request->reason ? false : null;
        $data->save();

        $similars = Track::where('type', 'keyword')
            ->where('value', $data->value)
            ->update(
                [
                    'error_reason' => $request->reason,
                    'valid' => $request->reason ? false : null
                ]
            );

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Error reason was updated'
            ]
        ];
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ]);

        $tracks = Track::whereIn('id', $request->id)->delete();

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

        $data = Track::with('user')
            ->where(function($query) use($request) {
                if ($request->search)
                {
                    $query->orWhere('value', 'ilike', '%'.$request->search.'%');
                    $query->orWhere('error_reason', 'ilike', '%'.$request->search.'%');
                }
            })
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => Track::count()
            ]
        ];
    }
}
