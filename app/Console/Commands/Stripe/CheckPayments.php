<?php

namespace App\Console\Commands\Stripe;

use Illuminate\Console\Command;

use App\Models\PaymentHistory;

use App\Jobs\PaymentCheckJob;

class CheckPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:payment:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stripe checks payments every minute.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $items = PaymentHistory::whereNotNull('session_id')
            ->where('amount', '>', 0)
            ->whereNull('status')
            ->get();

        if ($items)
        {
            foreach ($items as $item)
            {
                $this->info('Check '.$item->session_id);

                if ($item->expires_at > date('Y-m-d H:i:s'))
                    PaymentCheckJob::dispatch($item->user())->onQueue('paymentCheck');
                else
                {
                    $item->update([ 'status' => false ]);

                    $this->error('Payment failed');
                }
            }
        }
        else
            $this->error('No payment found to check.');
    }
}