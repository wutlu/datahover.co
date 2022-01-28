<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Payments;

use App\Jobs\PaymentCheckJob;

use App\Http\Requests\IdRequest;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
                'success_url' => route('payment', [ 'status' => 'success' ]),
                'cancel_url' => route('payment', [ 'status' => 'cancel' ]),
            ]
        );

        if ($session->url)
        {
            $sequence = Payments::orderBy('sequence', 'desc')->value('sequence') + 1;

            Payments::create(
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
     * Payment View
     * 
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function payment(Request $request)
    {
        PaymentCheckJob::dispatch($request->user())->onQueue('paymentCheck');

        if ($status = $request->status)
            return redirect()->route('payment')->with('status', $status);
        else
        {
            if ($status = session('status'))
                return view('payment', [ 'status' => $status ]);
            else
                return abort(404);
        }
    }

    /**
     * Payments View
     * 
     * @return view
     */
    public function payments()
    {
        return view('payments');
    }

    /**
     * Payments Data
     * 
     * @return object
     */
    public function paymentsData(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:1000',
            'skip' => 'required|integer|max:1000000',
            'take' => 'required|integer|max:1000',
        ]);

        $data = Payments::where('user_id', $request->user()->id)
            ->skip($request->skip)
            ->take($request->take)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => 'ok',
            'data' => $data,
            'stats' => [
                'total' => Payments::where('user_id', $request->user()->id)->count()
            ]
        ];
    }

    /**
     * Invoice View
     * 
     * @return view
     */
    public function invoice(string $key)
    {
        $payment = Payments::where('session_id', $key)->firstOrFail();

        $customer = new Party([
            'name' => $payment->meta['name'],
            'address' => $payment->meta['invoice_address'],
            'vat' => @$payment->meta['vat_id'] ?? null,
            'custom_fields' => [
                $payment->meta['zip_code'].' - '.$payment->meta['city'].' / '.$payment->meta['country']
            ],
        ]);

        if ($payment->status)
        {
            $notes = '';
        }
        else
        {
            $notes = implode('<br />', [
                'To make payments or view your invoices, go to the payments page.',
                '<a title="Payments" href="'.route('payments').'">'.route('payments').'</a>',
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
