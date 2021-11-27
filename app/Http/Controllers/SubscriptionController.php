<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Etsetra\Library\DateTime as DT;

use App\Models\Logs;
use App\Models\User;
use App\Models\PaymentHistory;
use App\Models\Track;

use App\Jobs\PaymentCheckJob;

class SubscriptionController extends Controller
{
    protected $plans;

    public function __construct()
    {
        $this->middleware('auth');

        $this->plans = config('plans');

        unset($this->plans['trial']);
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
        return [
            'success' => 'ok',
            'user' => [
                'balance' => $request->user()->balance(),
                'subscription' => $request->user()->subscription(),
                'auto_renew' => $request->user()->auto_renew ? true : false,
            ],
            'data' => array_values($this->plans),
        ];
    }

    /**
     * Subscription Start
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function start(Request $request)
    {
        $request->validate(
            [
                'plan' => 'required|string|in:'.implode(',', array_keys($this->plans))
            ]
        );

        $user = $request->user();
        $plan = config('plans')[$request->plan];

        if ($user->subscription == 'trial')
        {
            if ($user->balance() >= $plan['price'])
            {
                $user->subscription = $request->plan;
                $user->subscription_end_date = (new DT)->nowAt('+31 days');
                $user->save();

                $message = "Started subscription for plan ".$plan['name'].". \$".$plan['price']." has been deducted from your balance.";

                (new Logs)->enter($user->id, $message);

                PaymentHistory::create(
                    [
                        'user_id' => $user->id,
                        'amount' => -$plan['price'],
                        'expires_at' => (new DT)->nowAt('+1 days'),
                        'meta' => array_merge($plan, [ 'ip' => $request->ip() ]),
                        'status' => true,
                    ]
                );

                $current_tracks = Track::whereJsonContains('users', $user->id)->orderBy('id', 'desc')->get();

                if ($plan['track_limit'] < count($current_tracks))
                {
                    foreach ($current_tracks as $key => $track)
                    {
                        if (($key + 1) > $plan['track_limit'])
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

                    $deleted_count = count($current_tracks) - $plan['track_limit'];

                    (new Logs)->enter($user->id, "More than $deleted_count tracks were deleted.");
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

                (new Logs)->enter($user->id, $message);

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
        {
            $message = 'You are currently subscribed to the '.$user->subscription()->plan['name'].' plan. To start a new subscription, you need to cancel your current subscription.';

            (new Logs)->enter($user->id, $message);

            return [
                'success' => 'failed',
                'alert' => [
                    'type' => 'danger',
                    'message' => $message,
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

        if ($user->subscription == 'trial')
            return [
                'success' => 'failed',
                'toast' => [
                    'type' => 'danger',
                    'message' => 'You cannot cancel the trial package.'
                ]
            ];
        else
        {
            $plan = $user->subscription()->plan;

            $refund = number_format($user->subscription()->plan['price'] / 31 * $user->subscription()->days, 2, '.', '');
            // $refund = $refund - 5;

            (new Logs)->enter($user->id, $plan['name']." plan canceled. \$$refund refunded to your account.");

            $user->subscription = 'trial';
            $user->subscription_end_date = (new DT)->nowAt();
            $user->save();

            PaymentHistory::create(
                [
                    'user_id' => $user->id,
                    'amount' => $refund,
                    'expires_at' => (new DT)->nowAt('+1 days'),
                    'meta' => array_merge($plan, [ 'ip' => $request->ip() ]),
                    'status' => true,
                ]
            );

            return [
                'success' => 'ok',
                'redirect' => route('subscription.index')
            ];
        }
    }

    /**
     * Subscription Order
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function order(Request $request)
    {
        $request->validate(
            [
                'amount' => 'required|integer|min:10|max:50000',
                'name' => 'required|string|max:128',
                'country' => 'required|string|max:128|in:'.implode(',', config('locale.countries')),
                'city' => 'required|string|max:128',
                'zip_code' => 'required|string|max:48',
                'vat_id' => 'nullable|string|max:24',
                'phone' => 'required|string',
                'invoice_address' => 'required|string|max:255',
            ]
        );

        $session = $request->user()->checkout(
            [],
            [
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => config('cashier.currency'),
                            'product_data' => [
                                'name' => 'Balance for '.config('app.name'),
                            ],
                            'unit_amount' => intval($request->amount.'00'),
                        ],
                        'quantity' => 1
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('subscription.payment', [ 'status' => 'success' ]),
                'cancel_url' => route('subscription.payment', [ 'status' => 'cancel' ]),
            ]
        );

        if ($session->url)
        {
            PaymentHistory::create(
                [
                    'user_id' => $request->user()->id,
                    'session_id' => $session->id,
                    'amount' => $request->amount,
                    'expires_at' => $session->expires_at,
                    'meta' => [
                        'url' => $session->url,
                        'currency' => $session->currency,
                    ],
                    'status' => true,
                ]
            );

            return [
                'success' => 'ok',
                'redirect' => $session->url
            ];
        }
        else
            return [
                'success' => 'failed',
                'alert' => [
                    'type' => 'danger',
                    'message' => 'The payment process could not be started. The error has been reported to our support team. We will fix this issue as soon as possible and provide notification to you.'
                ]
            ];
    }

    /**
     * Subscription Payment
     * 
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function payment(Request $request)
    {
        PaymentCheckJob::dispatch($request->user())->onQueue('paymentCheck');

        if ($status = $request->status)
            return redirect()->route('subscription.payment')->with('status', $status);
        else
        {
            if ($status = session('status'))
                return view('payment', [ 'status' => $status ]);
            else
                return abort(404);
        }
    }

    /**
     * Payment History View
     * 
     * @return view
     */
    public function paymentHistory()
    {
        return view('payment_history');
    }

    /**
     * Payment History Data
     * 
     * @return object
     */
    public function paymentHistoryData(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = PaymentHistory::where('user_id', $request->user()->id)
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => PaymentHistory::where('user_id', $request->user()->id)->count()
            ]
        ];
    }
}
