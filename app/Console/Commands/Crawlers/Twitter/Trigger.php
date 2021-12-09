<?php

namespace App\Console\Commands\Crawlers\Twitter;

use Illuminate\Console\Command;

use App\Models\TwitterToken;
use App\Models\Option;
use App\Jobs\Crawlers\Twitter\StreamJob;

use Etsetra\Library\DateTime as DT;

class Trigger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:trigger';

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
        $tokens = TwitterToken::orderBy('id', 'asc')->get();

        foreach ($tokens as $token)
        {
            $this->newLine();

            $stop = false;
            $start = false;

            if ((new Option)->get('twitter.status', true) == 'on')
            {
                switch ($token->status)
                {
                    case 'working':
                        if ($token->pid && posix_getpgid($token->pid))
                            $this->line('Token is working #1');
                        else
                        {
                            $this->error('Token is not working, triggered to start #2');

                            $start = true;
                        }
                    break;
                    case 'not_working':
                        $this->line('Token stable #3');

                        $stop = $token->pid && posix_getpgid($token->pid) ? $token->pid : true;
                    break;
                    case 'start':
                        if ($token->pid && posix_getpgid($token->pid))
                        {
                            $this->line('Token already working #4');

                            $token->status = 'working';
                        }
                        else
                        {
                            $this->info('Token triggered to start #5');

                            $start = true;
                        }
                    break;
                    case 'stop':
                    case 'error':
                        if ($token->pid && posix_getpgid($token->pid))
                        {
                            $this->info('Token triggered to close #6');

                            $stop = $token->pid;
                        }
                        else
                        {
                            $this->error('The token is already not working #7');

                            $stop = true;
                        }
                    break;
                    case 'restart':
                        if ($token->pid && posix_getpgid($token->pid))
                        {
                            $this->info('Token triggered to close (by restart) #8');

                            $stop = $token->pid;
                        }
                        else
                        {
                            $this->line('The token is already not working (by restart) #9');

                            $stop = true;
                        }

                        $this->error('Token is not working, triggered to start #10');

                        $start = true;
                    break;
                }
            }
            else
            {
                $this->info('Twitter status is off #11');

                $stop = $token->pid && posix_getpgid($token->pid) ? $token->pid : true;
            }


            if (!$token->value)
            {
                $stop = $token->pid && posix_getpgid($token->pid) ? $token->pid : true;
                $start = false;
            }

            if ($stop)
            {
                if ($stop !== true)
                {
                    posix_kill($stop, SIGINT);

                    $this->info('Posix kill! #12');
                }

                if ($token->status == 'error')
                {
                    $this->error('Token status is error #13');
                }
                else
                {
                    if ($token->status != 'restart')
                    {
                        $token->tmp_key = null;
                        $token->value = null;
                        $token->pid = null;
                        $token->status = 'not_working';

                        $this->info('Stop token! #14');
                    }
                }
            }

            if ($start)
            {
                $token->status = 'start';

                StreamJob::dispatch($token->id)->onQueue('twitter')->delay(now()->addSeconds(5));

                $this->info('Start token! #15');
            }

            $token->save();

            $this->newLine();
        }
    }
}
