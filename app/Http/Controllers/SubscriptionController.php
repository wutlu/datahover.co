<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Etsetra\Library\DateTime as DT;

use App\Models\Logs;
use App\Models\User;

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
        $subscription = auth()->user()->subscription();

        return view('subscription', compact('subscription'));
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
                'balance' => $request->user()->balance,
                'subscription' => $request->user()->subscription()
            ],
            'data' => array_values($this->plans)
        ];
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
            $plan = $user->subscription()->plan['name'];
            $refund = number_format($user->subscription()->plan['price'] / 30 * $user->subscription()->days, 2, '.', '');

            (new Logs)->enter($user->id, "$plan plan canceled. \$$refund refunded to your account.");

            $user->subscription = 'trial';
            $user->subscription_end_date = (new DT)->nowAt();
            $user->balance = $user->balance + $refund;
            $user->save();

            return [
                'success' => 'ok',
                'redirect' => route('subscription.index')
            ];
        }
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
            if ($user->balance >= $plan['price'])
            {
                $user->subscription = $request->plan;
                $user->subscription_end_date = (new DT)->nowAt('+31 days');
                $user->balance = $user->balance - $plan['price'];
                $user->save();

                $message = "Started subscription for plan ".$plan['name'].". \$".$plan['price']." has been deducted from your balance.";

                (new Logs)->enter($user->id, $message);

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
     * Subscription Order
     * 
     * @param Illuminate\Http\Request $request
     * @return object
     */
    public function order(Request $request)
    {
        $user = $request->user();

        $session = $user->checkout(
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
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('subscription.payment', [ 'status' => 'success' ]),
                'cancel_url' => route('subscription.payment', [ 'status' => 'cancel' ]),
            ]
        );

        if ($url = @$session->url)
            return [
                'success' => 'ok',
                'redirect' => $url
            ];
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
}
