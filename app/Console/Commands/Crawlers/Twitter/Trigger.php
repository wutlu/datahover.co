<?php

namespace App\Console\Commands\Crawlers\Twitter;

use Illuminate\Console\Command;

use App\Models\TwitterToken;
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
        /**
         * 'working' çalışıyor
         * 'close' kapalı
         * 'error' hatalı
         * 'restart' tekrar başlatılacak
         * 'kill' durdurulacak
         * 'run' başlatılacak
         **/ 
        $tokens = TwitterToken::orderBy('id', 'asc')->get();

        foreach ($tokens as $token)
        {
            echo PHP_EOL;

            $start = false;

            $this->line("#$token->id: $token->screen_name ($token->device)");

            switch ($token->status)
            {
                case 'working':
                    if ($token->updated_at <= date('Y-m-d H:i:s', strtotime('-5 minutes')))
                    {
                        $token->status = 'restart';
                        $token->save();

                        $this->info('Status working to *restart* and triggered start.');

                        $start = true;
                    }
                    else
                        $this->info('Status is currently *working*');
                break;
                case 'close':
                    $this->line('Status is currently *close*');
                break;
                case 'error':
                    $this->info('Status is currently *error*');
                    $this->error($token->error_reason);
                break;
                case 'kill':
                    if ($token->updated_at <= date('Y-m-d H:i:s', strtotime('-5 minutes')))
                    {
                        $token->update(
                            [
                                'status' => 'close',
                                'tmp_key' => null,
                                'value' => null
                            ]
                        );

                        $this->info('Status *kill* to *close*');
                    }
                    else
                        $this->info('Will be *killed* but it needs to wait 5 minutes.');
                break;
                case 'restart':
                    if ($token->updated_at <= date('Y-m-d H:i:s', strtotime('-1 minutes')))
                    {
                        $start = true;

                        $this->info('Triggered start');
                    }
                    else
                        $this->info('Will be *restarted* but it needs to wait 1 minutes.');
                break;
                case 'run':
                    $start = true;

                    $this->info('Triggered start');
                break;
            }

            if ($start)
                StreamJob::dispatch($token->id)->onQueue('twitter');

            echo PHP_EOL;
        }
    }
}
