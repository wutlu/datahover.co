<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\PaymentHistory;

use Etsetra\Library\DateTime as DT;

class SubscriptionRenew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically extends expired subscriptions.';

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
        $users = User::whereDate('subscription_end_date', '<=', date('Y-m-d'))->get();

        foreach ($users as $user)
        {
            $this->info($user->email);

            if ($user->subscription()->days == 0)
            {
                if ($user->balance() >= $plan['price'])
                {
                    $user->subscription_end_date = (new DT)->nowAt('+31 days');
                    $user->save();

                    $message = "";

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
                }
                else
                {
                    // balance alert
                }
            }
        }
    }
}
