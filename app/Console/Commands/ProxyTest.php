<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

use App\Models\Proxy;
use App\Models\User;
use App\Notifications\ServerAlert;

class ProxyTest extends Command
{
    protected $api_url = 'http://ip-api.com/json';
    protected $timeout = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proxy speed test.';

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
        $this->line('Testing...');

        $proxies = Proxy::get();

        if (count($proxies))
        {
            $roots = User::where('is_root', true)->get();

            foreach ($proxies as $proxy)
            {
                $this->line($proxy->type.' '.$proxy->ip.':'.$proxy->port);

                $speed = $this->speed('tcp://'.$proxy->username.':'.$proxy->password.'@'.$proxy->ip.':'.$proxy->port);

                $this->info('Speed: '.$speed);

                $proxy->update([ 'speed' => $speed ]);

                if ($speed <= 1)
                {
                    Notification::send($roots, (new ServerAlert($proxy->type.' '.$proxy->ip.':'.$proxy->port.' proxy very slow!'))->onQueue('notifications'));
                }
            }
        }
        else
            $this->error('Proxy not found!');
    }

    /**
     * Check Proxy Speed
     *
     * @param string $proxy username:password@ip:port
     * @return integer
     */
    public function speed(string $proxy)
    {
        $starttime = microtime(true);

        try
        {
            $response = Http::timeout($this->timeout)->withHeaders([ 'proxy' => $proxy ])->get($this->api_url);

            $code = $response->status();
        }
        catch (\Exception $e)
        {
            $code = $this->timeout;
        }

        $load_time = $code == 200 ? intval(microtime(true) - $starttime) : $this->timeout;

        return $this->timeout - $load_time;
    }
}
