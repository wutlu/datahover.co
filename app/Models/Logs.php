<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Etsetra\Library\DateTime as DT;

class Logs extends Model
{
    protected $table = 'logs';

    public function enter(int $user_id, string $message, string $site = null)
    {
        $hash = $user_id.md5($message);

        $log = Logs::where('hash', $hash)->first();

        if ($log)
        {
            $log->repeat = $log->repeat+1;
            $log->updated_at = (new DT)->nowAt();
        }
        else
            $log = new Logs;

        $log->site = $site ?? config('app.domain');
        $log->user_id = $user_id;
        $log->message = $message;
        $log->hash = $hash;
        $log->save();
    }
}
