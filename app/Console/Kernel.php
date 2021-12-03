<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Option;

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
        # Proxy Check
        $schedule->command('proxy6:check')
                 ->dailyAt('00:00')
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground();
        # Proxy Test
        $schedule->command('proxy:test')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();



        # Subscription Auto Renew
        $schedule->command('subscription:renew')
                 ->hourlyAt(9)
                 ->withoutOverlapping()
                 ->runInBackground();



        # E-mail Alerts
        $schedule->command('email:alerts')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();



        # Twitter Organizer
        $schedule->command('twitter:organizer')
                 ->everyTenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('twitter.status', true) == 'on' ? false : true; });
        # Twitter Trigger
        $schedule->command('twitter:trigger')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('twitter.status', true) == 'on' ? false : true; });
        # Twitter Counter
        $schedule->command('twitter:counter')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('twitter.status', true) == 'on' ? false : true; });



        # News Detector
        $schedule->command('news:detector')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });
        # News Taker
        $schedule->command('news:taker')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });
        # News Buffer
        $schedule->command('news:buffer')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });
        # News Minuter
        $schedule->command('news:minuter')
                 ->everyThirtyMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });



        # Elasticsearch Check
        $schedule->command('elasticsearch:check')
                 ->everyTenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();
        # Etsetra/Elasticsearch/Console/BulkApi
        $schedule->command('elasticsearch:bulk:insert')
                 ->everyMinute()
                 ->runInBackground()
                 ->withoutOverlapping();



        # Run failed jobs
        $schedule->command('queue:failed')
                 ->everyTenMinutes()
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
