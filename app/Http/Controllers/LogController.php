<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Logs;

use Etsetra\Library\DateTime as DT;

class LogController extends Controller
{
    /**
     * @param string $site
     * @param string $message
     * @return object
     */
    public static function create(string $message, int $user_id)
    {
        $hash = md5($user_id.'_'.$message);

        $item = Logs::where('hash', $hash)->first();
        $item = $item ?? new Logs;

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
