<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use App\Models\Logs;

use App\Notifications\SimpleEmail;

class EmailAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification of user logs to users.';

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
        $logs = Logs::where('email_sent', false)
            ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime('-30 minutes')))
            ->get();

        if (count($logs))
        {
            foreach ($logs as $log)
            {
                $this->info($log->user->name.': '.$log->message);

                if ($log->user->email_alerts)
                {
                    Notification::send($log->user, (new SimpleEmail($log->message))->onQueue('notifications'));

                    $this->info($log->id.' - E-mail sent');
                }
                else
                    $this->error('E-mail notifications are turned off');

                $log->update([ 'email_sent' => true ]);
            }
        }
        else
            $this->info('E-mail area clean');
    }
}
