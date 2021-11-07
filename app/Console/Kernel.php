<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('proxy6:check')
                ->dailyAt('00:00')
                ->timezone(config('app.timezone'))
                ->withoutOverlapping()
                ->runInBackground();

        $schedule->command('proxy:test')
                 ->everyMinute()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground();

        $schedule->command('elasticsearch:check')
                 ->everyTenMinutes()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground();

        $schedule->command('crawler:twitter:organizer')
                 ->everyTenMinutes()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
