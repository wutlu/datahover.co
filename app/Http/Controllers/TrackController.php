<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use App\Models\Track;
use App\Models\Logs;

use App\Http\Middleware\TokenCheck;

class TrackController extends Controller
{
    public $sources;
    public $track_types;
    public $create_rules;
    public $list_rules;
    public $delete_rules;
    public $rate_minutes = 10;
    public $rate_limit = 100;

    public function __construct()
    {
        $this->middleware('auth')->only([ 'dashboard' ]);

        $this->sources = array_keys(config('sources'));
        $this->track_types = array_unique(array_keys(Arr::collapse(Arr::pluck(config('sources'), 'tracks'))));
        $this->create_rules = [
            'source' => 'required|string|in:'.implode(',', $this->sources),
            'type' => 'required|string|in:'.implode(',', $this->track_types),
            'value' => 'required|string|max:255',
        ];
        $this->list_rules = [
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
            'source' => 'required|array',
            'source.*' => 'required_with:source|string|in:'.implode(',', $this->sources)
        ];
        $this->delete_rules = [
            'id' => 'required|array',
            'id.*' => 'required_with:id|integer',
        ];
    }

    public function dashboard()
    {
        $apis = [
            'trackList' => [
                'name' => 'Track List APi',
                'method' => 'POST',
                'route' => route('api.track.list'),
                'params' => $this->list_rules,
            ],
            'trackCreate' => [
                'name' => 'Track Create APi',
                'method' => 'POST',
                'route' => route('api.track.create'),
                'params' => $this->create_rules,
            ],
            'trackDelete' => [
                'name' => 'Track Delete APi',
                'method' => 'POST',
                'route' => route('api.track.delete'),
                'params' => $this->delete_rules,
            ],
        ];

        return view('tracks', [
            'rate_minutes' => $this->rate_minutes,
            'rate_limit' => $this->rate_limit,
            'apis' => $apis,
        ]);
    }

    public function createApi(Request $request)
    {
        $total_track = Track::where('user_id', $request->user->id)->count();

        if ($total_track < $request->user->subscription()->package['track_limit'])
        {
            $request->validate(
                array_merge(
                    $this->create_rules,
                    [
                        'type' => 'required|string|in:'.implode(',', array_keys(config('sources.'.($request->source ?? 'twitter').'.tracks'))),
                        'value' => config("sources.$request->source.tracks.$request->type")
                    ]
                )
            );

            $pattern = str_replace('regex:', '', array_values(Arr::where(config("sources.$request->source.tracks.$request->type"), function($value, $key) {
                return Str::startsWith($value, 'regex');
            }))[0]);

            preg_match($pattern, $request->value, $match);

            if ($request->type != 'keyword')
                $request->value = Str::of($match[0])->finish('/');

            $q = Track::where(
                [
                    'user_id' => $request->user->id,
                    'source' => $request->source,
                    'type' => $request->type,
                    'value' => $request->value,
                ]
            )->exists();

            if ($q)
            {
                return [
                    'success' => 'failed',
                    'alert' => [
                        'type' => 'warning',
                        'message' => 'This record already exists.'
                    ],
                ];
            }
            else
            {
                $q = new Track;
                $q->user_id = $request->user->id;
                $q->source = $request->source;
                $q->type = $request->type;
                $q->value = $request->value;
                $q->save();

                (new Logs)->enter($request->user->id, $request->ip().': createTrack {"'.$request->source.':'.$request->type.'","'.$request->value.'"}');

                return [
                    'success' => 'ok',
                    'alert' => [
                        'type' => 'success',
                        'message' => 'Tracking record created!'
                    ],
                ];
            }
        }
        else
        {
            return [
                'success' => 'failed',
                'alert' => [
                    'type' => 'danger',
                    'message' => 'Your subscription does not support more.'
                ]
            ];
        }
    }

    public function listApi(Request $request)
    {
        $request->validate($this->list_rules);

        $data = Track::select(
                'id',
                'source',
                'type',
                'value',
                'error_hit',
                'error_reason',
                'request_hit',
                'request_frequency',
                'request_at',
                'valid',
            )
            ->where(function($query) use($request) {
                if ($request->search)
                    $query->orWhere('value', 'ilike', '%'.$request->search.'%');
            })
            ->whereIn('source', $request->source)
            ->where('user_id', $request->user->id)
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'track' => [
                'limit' => $request->user->subscription()->package['track_limit'],
                'total' => Track::where('user_id', $request->user->id)->count()
            ]
        ];
    }

    public function deleteApi(Request $request)
    {
        $request->validate($this->delete_rules);

        $tracks = Track::where('user_id', $request->user->id)->whereIn('id', $request->id)->get();

        foreach ($tracks as $track)
        {
            (new Logs)->enter($request->user->id, $request->ip().': deleteTrack {"'.$track->source.':'.$track->type.'","'.$track->value.'"}');

            $track->delete();
        }

        return [
            'success' => 'ok',
            'alert' => [
                'type' => 'success',
                'message' => 'Selected successfully deleted'
            ]
        ];
    }
}
