<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use Etsetra\Elasticsearch\Client;

use App\Notifications\ServerAlert;
use App\Models\User;

class CheckElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check elasticsearch server health';

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
        $clusters = (new Client)->cat('health');

        if ($clusters->success == 'ok')
        {
            foreach ($clusters->data as $cluster)
            {
                if ($cluster['status'] == 'red')
                    $this->error($this->notification('Cluster status color red case'));
                else
                {
                    $nodes = (new Client)->cat('nodes');

                    if ($nodes->success == 'ok')
                    {
                        $indices = (new Client)->cat('indices');

                        if ($indices->success == 'ok')
                        {
                            $message = [];

                            foreach ($indices->data as $index)
                            {
                                if ($index['health'] != 'green')
                                {
                                    $message[] = $index['index'].': '.$index['health'];
                                }
                            }

                            if (count($message))
                                $this->error('There are non-green indexes. '.$this->notification(implode(', ', $message)));
                            else
                                $this->info('Everything is OK');
                        }
                        else
                            $this->error($this->notification('Failed to connect to Elasticsearch indices'));
                    }
                    else
                        $this->error($this->notification('Failed to connect to Elasticsearch nodes'));
                }
            }
        }
        else
            $this->error($this->notification('Failed to connect to Elasticsearch clusters'));
    }

    /**
     * Roots Notification
     * 
     * @param string $message
     * @return string
     */
    private function notification(string $message)
    {
        $roots = User::where('is_root', true)->get();

        Notification::send($roots, (new ServerAlert($message))->onQueue('notifications'));

        return $message;
    }
}
