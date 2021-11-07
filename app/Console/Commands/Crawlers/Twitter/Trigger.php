<?php

namespace App\Console\Commands\Crawlers\Twitter;

use Illuminate\Console\Command;

use App\Models\TwitterToken;
use App\Jobs\Crawlers\Twitter\StreamJob;

class Trigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:twitter:trigger';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Twitter track trigger.';

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
        $tokens = TwitterToken::whereIn('status', [ 'restart', 'run', 'kill' ])->get();

        foreach ($tokens as $token)
        {
            if ($token->status == 'restart' || $token->status == 'run')
            {
                StreamJob::dispatch()->onQueue('twitter');
            }
        }
    }
}
