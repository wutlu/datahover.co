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
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground();



        # Twitter Organizer
        $schedule->command('twitter:organizer')
                 ->everyTenMinutes()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('twitter.status', true) == 'on' ? false : true; });
        # Twitter Trigger
        $schedule->command('twitter:trigger')
                 ->everyMinute()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('twitter.status', true) == 'on' ? false : true; });
        # Twitter Counter
        $schedule->command('twitter:counter')
                 ->everyThirtyMinutes()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('twitter.status', true) == 'on' ? false : true; });



        # News Detector
        $schedule->command('news:detector')
                 ->everyMinute()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });
        # News Taker
        $schedule->command('news:taker')
                 ->everyMinute()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });
        # News Buffer
        $schedule->command('news:buffer')
                 ->everyMinute()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });
        # News Minuter
        $schedule->command('news:minuter')
                 ->everyThirtyMinutes()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground()
                 ->skip(function() { return (new Option)->get('news.status', true) == 'on' ? false : true; });



        # Elasticsearch Check
        $schedule->command('elasticsearch:check')
                 ->everyTenMinutes()
                 ->timezone(config('app.timezone'))
                 ->withoutOverlapping()
                 ->runInBackground();
        # Etsetra/Elasticsearch/Console/BulkApi
        $schedule->command('elasticsearch:bulk:insert')
                 ->everyMinute()
                 ->runInBackground()
                 ->withoutOverlapping();
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
