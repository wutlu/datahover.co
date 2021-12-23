<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use Closure;
use Route;

use App\Models\User;

use Etsetra\Library\DateTime as DT;

class TokenCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $api_key = $request->header('X-Api-Key');
        $api_secret = $request->header('X-Api-Secret');

        $user = User::where([ 'api_key' => $api_key, 'api_secret' => $api_secret ])->first();

        if ($user)
        {
            if ($user->subscription()->days)
            {
                $action = Route::currentRouteAction();
                $controller = new (explode('@', $action)[0]);
                $rate_minutes = $controller->rate_minutes;
                $rate_limit = $controller->rate_limit;

                $route_key = 'api_requests:'.md5($action).':'.$user->id;

                if ($hit = Redis::get($route_key))
                {
                    if ($hit < $rate_limit)
                        Redis::command('incr', [ $route_key ]);
                    else
                    {
                        return response()->json([
                            'success' => 'failed',
                            'alert' => [
                                'type' => 'danger',
                                'message' => "Please wait a moment. You can send $rate_limit request in $rate_minutes minutes.",
                            ]
                        ]);
                    }
                }
                else
                {
                    Redis::command('set', [ $route_key, 1 ]);
                    Redis::command('expire', [ $route_key, $rate_minutes * 60 ]);
                }


                $request->user = $user;
            }
            else
            {
                return response()->json([
                    'success' => 'failed',
                    'alert' => [
                        'type' => 'danger',
                        'message' => 'Your subscription has expired. Please renew your subscription.'
                    ]
                ]);
            }
        }
        else
        {
            return response()->json([
                'success' => 'failed',
                'alert' => [
                    'type' => 'danger',
                    'message' => 'Invalid api key or secret'
                ]
            ]);
        }

        return $next($request);
    }
}
