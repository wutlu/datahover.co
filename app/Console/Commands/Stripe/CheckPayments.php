<?php

namespace App\Console\Commands\Stripe;

use Illuminate\Console\Command;

use App\Models\Payments;
use App\Models\User;

use App\Jobs\PaymentCheckJob;

class CheckPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:payments:check';

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
        $items = Payments::whereNotNull('session_id')
            ->where('amount', '>', 0)
            ->whereNull('status')
            ->get();

        if ($items)
        {
            foreach ($items as $item)
            {
                $this->info('Check '.$item->session_id);

                $user = User::find($item->user_id);

                if ($item->expires_at > date('Y-m-d H:i:s'))
                    PaymentCheckJob::dispatch($user)->onQueue('paymentCheck');
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
