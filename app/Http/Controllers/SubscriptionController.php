<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Etsetra\Library\DateTime as DT;

use App\Models\PaymentHistory;
use App\Models\Track;
use App\Models\Plan;

use App\Http\Requests\IdRequest;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Subscription Dashboard
     * 
     * @return view
     */
    public function view()
    {
        return view('subscription', [ 'subscription' => auth()->user()->subscription() ]);
    }

    /**
     * Subscription Details
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function details(Request $request)
    {
        $user = $request->user();

        $last_invoice = PaymentHistory::where('user_id', $user->id)
            ->whereNotNull('session_id')
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->first();

        return [
            'success' => 'ok',
            'user' => [
                'balance' => $user->balance(),
                'subscription' => $user->subscription(),
            ],
            'data' => Plan::orWhere('user_id', $user->id)->orWhereIn('name', [ 'Basic', 'Enterprise' ])->get(),
            'last_invoice' => $last_invoice ?? null
        ];
    }

    /**
     * Subscription Start
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function start(IdRequest $request)
    {
        $user = $request->user();
        $subscription = $user->subscription();

        if ($subscription->plan->price > 0)
        {
            $message = 'You are currently subscribed to the '.$subscription->plan->name.' plan. To start a new subscription, you need to cancel your current subscription.';

            LogController::create(config('app.domain'), $message, $user->id);

            return [
                'success' => 'failed',
                'alert' => [
                    'type' => 'danger',
                    'message' => $message,
                ]
            ];
        }
        else
        {
            $plan = Plan::where('price', '>', 0)
                ->where(function($query) use($user) {
                    $query->orWhereNull('user_id');
                    $query->orWhere('user_id', $user->id);
                })
                ->find($request->id);

            if ($plan)
            {
                if ($user->balance() >= $plan->price)
                {
                    $user->plan_id = $request->id;
                    $user->subscription_end_date = (new DT)->nowAt('+31 days');
                    $user->save();

                    LogController::create(
                        config('app.domain'),
                        'Started subscription for plan '.$plan->name.'. '.config('cashier.currency_symbol').$plan->price.' has been deducted from your balance.',
                        $user->id
                    );

                    PaymentHistory::create(
                        [
                            'user_id' => $user->id,
                            'amount' => -$plan->price,
                            'expires_at' => (new DT)->nowAt('+1 days'),
                            'meta' => array_merge($plan->toArray(), [ 'ip' => $request->ip() ]),
                            'status' => true,
                        ]
                    );

                    $current_tracks = Track::whereJsonContains('users', $user->id)->orderBy('id', 'desc')->get();

                    if ($plan->track_limit < count($current_tracks))
                    {
                        foreach ($current_tracks as $key => $track)
                        {
                            if (($key + 1) > $plan->track_limit)
                            {
                                if (count($track->users) == 1)
                                    $track->delete();
                                else
                                {
                                    $array = $track->users;

                                    if (($key = array_search($user->id, $array)) !== false)
                                        unset($array[$key]);

                                    $track->users = array_values($array);
                                    $track->save();
                                }
                            }
                        }

                        $deleted_count = count($current_tracks) - $plan->track_limit;

                        LogController::create(config('app.domain'), "More than $deleted_count tracks were deleted.", $user->id);
                    }

                    return [
                        'success' => 'ok',
                        'toast' => [
                            'type' => 'success',
                            'message' => 'Subscription started',
                        ],
                        'redirect' => route('subscription.index')
                    ];
                }
                else
                {
                    $message = 'Your balance is not sufficient for this subscription. Please add balance to your account above.';

                    LogController::create(config('app.domain'), $message, $user->id);

                    return [
                        'success' => 'failed',
                        'alert' => [
                            'type' => 'danger',
                            'message' => $message,
                        ]
                    ];
                }
            }
            else
                return [
                    'success' => 'failed',
                    'alert' => [
                        'type' => 'danger',
                        'message' => 'You cannot switch to this plan'
                    ]
                ];
        }
    }

    /**
     * Subscription Cancel
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $subscription = $user->subscription();
        $plan = $subscription->plan;

        if ($plan->price > 0)
        {
            $refund = number_format($plan->price / 31 * $subscription->days, 2, '.', '');
            // $refund = $refund - 5;

            $user->plan_id = null;
            $user->subscription_end_date = (new DT)->nowAt();
            $user->save();

            PaymentHistory::create(
                [
                    'user_id' => $user->id,
                    'amount' => $refund,
                    'expires_at' => (new DT)->nowAt('+1 days'),
                    'meta' => array_merge($plan->toArray(), [ 'ip' => $request->ip() ]),
                    'status' => true,
                ]
            );

            LogController::create(
                config('app.domain'),
                $plan->name.' plan canceled. '.config('cashier.currency_symbol').$refund.' refunded to your account.',
                $user->id
            );

            return [
                'success' => 'ok',
                'redirect' => route('subscription.index')
            ];
        }
        else
            return [
                'success' => 'failed',
                'toast' => [
                    'type' => 'danger',
                    'message' => 'You cannot cancel the trial package.'
                ]
            ];
    }
}
