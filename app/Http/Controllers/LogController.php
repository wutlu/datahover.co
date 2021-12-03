<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Logs;

use Etsetra\Library\DateTime as DT;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only([ 'list' ]);
    }

    /**
     * List Api
     * 
     * @param Illuminate\Http\Request $request
     * @return view
     */
    public function list(Request $request)
    {
        $request->validate(
            [
                'search' => 'nullable|string|max:1000',
                'skip' => 'required|integer|max:1000000',
                'take' => 'required|integer|max:1000',
            ]
        );

        $data = Logs::select('id', 'site', 'message', 'repeat', 'updated_at')
            ->where(function($query) use($request) {
                if ($request->search)
                {
                    $query->orWhere('message', 'ilike', '%'.$request->search.'%');
                    $query->orWhere('site', 'ilike', '%'.$request->search.'%');
                }
            })
            ->where('user_id', $request->user()->id)
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('updated_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data
        ];
    }

    /**
     * @param string $site
     * @param string $message
     * @return object
     */
    public static function create(string $site, string $message, int $user_id)
    {
        $hash = md5($user_id.'_'.$message);

        $item = Logs::where('hash', $hash)->first();
        $item = $item ?? new Logs;

        $item->site = $site;
        $item->user_id = $user_id;
        $item->message = $message;
        $item->hash = $hash;
        $item->repeat = $item->repeat + 1;
        $item->ip = request()->ip();
        $item->updated_at = (new DT)->nowAt();
        $item->email_sent = false;
        $item->save();

        return (object) [
            'success' => 'ok'
        ];
    }
}
