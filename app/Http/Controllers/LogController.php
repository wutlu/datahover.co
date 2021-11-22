<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Logs;

class LogController extends Controller
{
    public $list_rules;
    public $rate_minutes = 1;
    public $rate_limit = 100;

    public function __construct()
    {
        // $this->middleware('auth');

        $this->list_rules = [
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ];
    }

    /**
     * List Api
     * 
     * @return view
     */
    public function listApi(Request $request)
    {
        $request->validate($this->list_rules);

        $data = Logs::select('id', 'site', 'message', 'repeat', 'created_at')
            ->where(function($query) use($request) {
                if ($request->search)
                {
                    $query->orWhere('message', 'ilike', '%'.$request->search.'%');
                    $query->orWhere('site', 'ilike', '%'.$request->search.'%');
                }
            })
            ->where('user_id', $request->user->id)
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
        $hash = md5($message);

        $item = Logs::where('hash', $hash)->first();
        $item = $item ?? new Logs;

        $item->site = $site;
        $item->user_id = $user_id;
        $item->message = $message;
        $item->hash = $hash;
        $item->repeat = $item->repeat + 1;
        $item->save();

        return (object) [
            'success' => 'ok'
        ];
    }
}
