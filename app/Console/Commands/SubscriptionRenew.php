<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Models\Plan;
use App\Models\Payments;

use Etsetra\Library\DateTime as DT;

use App\Http\Controllers\LogController;

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
        $trial_plan_ids = Plan::where('price', '>', 0)->pluck('id')->toArray();

        $users = User::whereNotIn('plan_id', $trial_plan_ids)->get();

        foreach ($users as $user)
        {
            $subscription = $user->subscription();
            $plan = $subscription->plan;

            $this->info($user->email.' ('.$subscription->days.' days left)');

            if ($subscription->days <= 1)
            {
                if ($user->balance() >= $plan->price)
                {
                    $user->subscription_end_date = (new DT)->nowAt('+31 days');
                    $user->save();

                    Payments::create(
                        [
                            'user_id' => $user->id,
                            'amount' => -$plan->price,
                            'expires_at' => (new DT)->nowAt('+1 days'),
                            'meta' => array_merge($plan->toArray(), [ 'log' => 'Auto Renew' ]),
                            'status' => true,
                        ]
                    );

                    $message = 'Your subscription has been renewed for '.config('cashier.currency_symbol').$plan->price.'.';

                    $this->info($message);
                }
                else
                {
                    $message = 'Your account balance is insufficient. Your subscription could not be renewed.';

                    $this->error($message);
                }

                LogController::create(config('app.domain'), $message, $user->id);
            }
            else
                $this->error('The payment day has not come.');
        }
    }
}
