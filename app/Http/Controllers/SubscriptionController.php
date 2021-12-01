<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Etsetra\Library\DateTime as DT;

use App\Models\Logs;
use App\Models\User;
use App\Models\PaymentHistory;
use App\Models\Track;
use App\Models\Plan;

use App\Jobs\PaymentCheckJob;

use Stripe\Stripe;
use Stripe\InvoiceItem;

use App\Http\Requests\IdRequest;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;

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
                'terms' => 'required|accepted',
                'amount' => 'required|integer|min:10|max:50000',
                'name' => 'required|string|max:128',
                'country' => 'required|string|max:128|in:'.implode(',', config('locale.countries')),
                'city' => 'required|string|max:128',
                'zip_code' => 'required|string|max:48',
                'vat_id' => 'nullable|string|max:24',
                'phone' => 'required|string|max:16',
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
            $sequence = PaymentHistory::orderBy('sequence', 'desc')->value('sequence') + 1;

            PaymentHistory::create(
                [
                    'user_id' => $request->user()->id,
                    'session_id' => $session->id,
                    'amount' => $request->amount,
                    'expires_at' => $session->expires_at,
                    'meta' => [
                        'url' => $session->url,
                        'currency' => $session->currency,
                        'amount' => $request->amount,
                        'name' => $request->name,
                        'country' => $request->country,
                        'city' => $request->city,
                        'zip_code' => $request->zip_code,
                        'vat_id' => $request->vat_id,
                        'phone' => $request->phone,
                        'invoice_address' => $request->invoice_address,
                        'ip' => $request->ip(),
                    ],
                    'series' => 'AA',
                    'sequence' => $sequence,
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

    /**
     * Invoice
     * 
     * @return view
     */
    public function invoice(string $key)
    {
        $payment = PaymentHistory::where('session_id', $key)->firstOrFail();

        $customer = new Party([
            'name' => $payment->meta['name'],
            'address' => $payment->meta['invoice_address'],
            'custom_fields' => [
                'Country' => $payment->meta['zip_code'].' - '.$payment->meta['city'].' / '.$payment->meta['country'],
                'Vat Id' => $payment->meta['vat_id'],
            ],
        ]);

        if ($payment->status)
        {
            $notes = '';
        }
        else
        {
            $notes = implode('<br />', [
                'To make payments or view your invoices, go to the payment history page.',
                '<a href="'.route('subscription.payment.history').'">'.route('subscription.payment.history').'</a>',
            ]);
        }

        $items = [
            Invoice::makeItem('Datahover balance')->pricePerUnit($payment->amount)
        ];

        $invoice = Invoice::make('receipt')
            ->series($payment->series)
            ->sequence($payment->sequence)
            ->status($payment->status ? __('invoices::invoice.paid') : __('invoices::invoice.due'))
            ->serialNumberFormat('{SERIES} {SEQUENCE}')
            ->buyer($customer)
            ->date($payment->created_at)
            ->dateFormat('d/m/Y')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->addItems($items)
            ->notes($notes)
            ->currencySymbol(config('cashier.currency_symbol'))
            ->currencyCode(config('cashier.currency'))
            ->logo(public_path('images/logo.png'))
            ->save('public');

        return $invoice->stream();
    }
}
