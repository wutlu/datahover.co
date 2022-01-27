<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Payments;
use App\Models\User;

use Etsetra\Library\DateTime as DT;

use App\Http\Controllers\LogController;

class PaymentCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $histories = Payments::whereNotNull('session_id')
            ->where('amount', '>', 0)
            ->whereNull('status')
            ->where('user_id', $this->user->id)
            ->get();

        foreach ($histories as $payment)
        {
            if ($payment->expires_at >= date('Y-m-d', strtotime('-24 hours')))
            {
                $session = $this->user->stripe()->checkout->sessions->retrieve($payment->session_id);
                $payment->status = $session->status == 'complete' ? true : false;

                if ($session->status == 'complete')
                    LogController::create('Your '.config('cashier.currency_symbol').$payment->amount.' payment has been defined to your account.', $this->user->id);
                else
                    LogController::create('Your payment could not be received', $this->user->id);
            }
            else
                $payment->status = false;

            $payment->save();
        }
    }
}
