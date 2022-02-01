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


        # Clear Old Datas
        $schedule->command('data:clear')
                 ->dailyAt('00:00')
                 ->timezone(config('app.timezone'))
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
        # Twitter Counter
        $schedule->command('twitter:counter')
                 ->everyTenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('twitter.status', true) == 'on' ? false : true; });
        # Twitter Trigger
        $schedule->command('twitter:trigger')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();



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
                 ->everyTenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });



        # YouTube Trigger
        $schedule->command('youtube:taker')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('youtube.status', true) == 'on' ? false : true; });
        # YouTube Minuter
        $schedule->command('youtube:minuter')
                 ->everyTenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('youtube.status', true) == 'on' ? false : true; });



        # Instagram Trigger
        $schedule->command('instagram:taker2')
                 ->everyFiveMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('instagram.status', true) == 'on' ? false : true; });
        # Instagram Minuter
        $schedule->command('instagram:minuter')
                 ->everyTenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('instagram.status', true) == 'on' ? false : true; });



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



        # Generate Feed Files
        $schedule->command('feeds:generate')
                 ->everyFifteenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();



        # Generate Sitemap
        $schedule->command('sitemap:generate')
            ->daily()
            ->withoutOverlapping()
            ->runInBackground();



        # Run failed jobs
        $schedule->command('queue:failed')
                 ->everyTenMinutes()
                 ->withoutOverlapping()
                 ->runInBackground();

        # Check Payment
        $schedule->command('stripe:payments:check')
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
