<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

use Laravel\Socialite\Facades\Socialite;

use Etsetra\Library\DateTime as DT;

use App\Models\User;
use App\Models\Logs;
use App\Models\HideInfo;
use App\Notifications\ServerAlert;

use Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->only(
            [
                'gate',
                'gateRedirect',
                'gateCallback',
            ]
        );
        $this->middleware('auth')->only(
            [
                'account',
                'apiSecretGenerator',
                'info',
                'gateExit',
                'emailAlerts',
            ]
        );
    }

    /**
     * User driver info
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function info(Request $request)
    {
        $keys = [
            'search.api',
            'subscription.balance',
            'track.list',
        ];

        $request->validate(
            [
                'key' => 'nullable|string|in:'.implode(',', $keys),
                'save' => 'nullable|string|in:on'
            ]
        );

        $exists = HideInfo::where([ 'user_id' => $request->user()->id, 'key' => $request->key ])->exists();

        if (!$exists && $request->save)
        {
            HideInfo::firstOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'key' => $request->key
                ] 
            );
        }

        return [
            'success' => 'ok',
            'data' => [
                'have' => $exists
            ]
        ];
    }

    /**
     * E-mail Alerts
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function emailAlerts()
    {
        $user = auth()->user();
        $user->email_alerts = $user->email_alerts ? false : true;
        $user->save();

        return [
            'success' => 'ok'
        ];
    }

    /**
     * Account View
     * 
     * @return view
     */
    public function account()
    {
        return view('account', [ 'emailAlerts' => auth()->user()->email_alerts ]);
    }

    /**
     * Api Secret Generator
     * 
     * @return object
     */
    public function apiSecretGenerator()
    {
        $secret = Str::random(64);

        $user = auth()->user();
        $user->api_secret = $secret;
        $user->save();

        $message = 'Api Secret Regenerated';

        LogController::create($message, $user->id);

        return [
            'success' => 'ok',
            'data' => [
                'api_secret' => $secret
            ],
            'alert' => [
                'message' => 'Api Secret Regenerated'
            ]
        ];
    }

    /**
     * Gate View
     * 
     * @return view
     */
    public function gate()
    {
        return view('gate');
    }

    /**
     * Gate Redirect
     * 
     * @return object
     */
    public function gateRedirect(Request $request)
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Gate Callback
     * 
     * @return object
     */
    public function gateCallback(Request $request)
    {
        try
        {
            $data = Socialite::driver('github')->user();
        }
        catch (\Exception $e)
        {
            Session::flash('error', 'Failed to get login information from Github!');

            return view('gate');
        }

        $user = User::find($data->id);

        if (!$user)
        {
            $user = User::where('email', $data->email)->first();

            if (!$user)
            {
                $user = new User;
                $user->id = $data->id;
                $user->name = $data->nickname;
                $user->email = $data->email;
                $user->avatar = $data->avatar;
                $user->subscription_end_date = (new DT)->nowAt('+4 days');
                $user->api_key = $data->id.'-'.Str::random(10);
                $user->api_secret = Str::random(64);
                $user->save();

                LogController::create('Welcome to Etsetra', $user->id);

                $roots = User::where('is_root', true)->get();

                Notification::send($roots, (new ServerAlert('New member! ('.$user->name.') '.$user->email))->onQueue('notifications'));
            }
        }

        Auth::loginUsingId($user->id);

        $message = 'You are logged into the Dashboard';

        LogController::create($message, $user->id);

        return redirect()->route('dashboard');
    }

    /**
     * Gate Exit
     * 
     * @return object
     */
    public function gateExit()
    {
        $message = 'You are logged out of Dashboard';

        LogController::create($message, auth()->user()->id);

        Auth::logout();

        return redirect()->route('index');
    }
}
