<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

use App\Models\Option;
use App\Models\Proxy;
use App\Models\User;

use App\Notifications\ServerAlert;

use Carbon\Carbon;

use Etsetra\Library\DateTime as DT;

class Proxy6 extends Command
{
    protected $base = 'https://proxy6.net/api/';

    protected $options = [];
    protected $errors = [];

    protected $all_proxies = [];
    protected $active_proxies = [];
    protected $expiry_proxies = [];

    protected $current_balance = 0;
    protected $required_proxies = 0;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proxy6:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Controls proxy retrieval with proxy6.net APIs.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        try
        {
            $this->options = (new Option)->get('proxy.*');
        }
        catch (\Exception $e)
        {
            //
        }

        $this->errors = [
            30 => 'Unknown error',
            100 => 'Authorization error, wrong key',
            110 => 'Wrong method',
            200 => 'Wrong proxies quantity, wrong amount or no quantity input',
            210 => 'Period error, wrong period input (days) or no input',
            220 => 'Country error, wrong country input (iso2 for country input) or no input',
            230 => 'Error of the list of the proxy numbers. Proxy numbers have to divided with comas',
            250 => 'Tech description error',
            260 => 'Proxy type (protocol) error. Incorrect or missing',
            300 => 'Proxy amount error. Appears after attempt of purchase of more proxies than available on the service',
            400 => 'Balance error. Zero or low balance on your account',
            404 => 'Element error. The requested item was not found',
            410 => 'Error calculating the cost. The total cost is less than or equal to zero',
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $roots = User::where('is_root', true)->get();

        /**
         * Bağlan
         */
        $getProxies = $this->connect('getproxy');

        $this->current_balance = $getProxies->balance;

        /**
         * Detayları sınıfa doldur.
         */
        $this->fill($getProxies->list);

        /**
         * Ekrana bas
         */
        $this->table(
            [ 'Title', 'Value' ],
            [
                [ 'Total', count($this->all_proxies) ],
                [ 'Active', count($this->active_proxies) ],
                [ 'Expiry', count($this->expiry_proxies) ],
                [ 'Required', $this->required_proxies ]
            ]
        );

        $this->info('Proxy6 Balance: $'.$this->current_balance);

        /**
         * İhtiyaç varsa satın alma işlemi yap.
         */
        if ($this->required_proxies > 0)
        {
            $buyOptions = [
                'count' => $this->required_proxies,
                'period' => $this->options['proxy.buy_period'],
                'country' => $this->options['proxy.proxy_country'],
                'version' => $this->options['proxy.proxy_version'],
            ];

            $getPrice = $this->connect('getprice', $buyOptions);

            if ($getPrice->balance >= $getPrice->price)
            {
                $buyProxy = $this->connect('buy', $buyOptions);

                /**
                 * Yöneticilere durum maili gönder.
                 */
                Notification::send($roots, (new ServerAlert(count($buyProxy->list).' proxies were received over https://proxy6.net'))->onQueue('notifications'));

                $this->info(count($buyProxy->list) > 1 ? 'Proxies Received' : 'Proxy Received');

                $this->current_balance = $buyProxy->balance;
            }
            else
            {
                /**
                 * Yöneticilere durum maili gönder. / Bakiye yetersiz.
                 */
                Notification::send($roots, (new ServerAlert('current https://proxy6.net balance is $'.$getPrice->balance.' Please load the balance as soon as possible.'))->onQueue('notifications'));

                $this->error('Proxy6 Purchase Failed');
            }
        }
        else
        {
            /**
             * Bakiye azalmışsa yöneticileri uyar.
             */
            if ($this->current_balance < $this->options['proxy.min_balance_for_alert'])
            {
                Notification::send($roots, (new ServerAlert('https://proxy6.net balance is currently $'.$this->current_balance.'. Please load the balance as soon as possible.'))->onQueue('notifications'));
    
                $this->error('Send "Low Balance Alert" to all roots');
            }
        }

        (new Option)->change('proxy.current_balance', $this->current_balance);

        /**
         * Ömrü bitmiş proxyleri db'den sil.
         */
        Proxy::where('expiry_date', '<=', date('Y-m-d H:i:s', strtotime('+1 days')))->delete();
    }

    /**
     * Proxy6 API'lerine bağlan.
     *
     * @var string $method
     * @var array $params
     * @return mix
     */
    private function connect(string $method = '/', array $params = [])
    {
        $this->info("Connect: $method");

        $response = Http::get($this->base.$this->options['proxy.api_key'].'/'.$method, $params);

        try
        {
            $response = (object) $response->json();

            if ($response->status == 'yes')
            {
                if (@$response->list)
                    $this->db($response->list);

                return $response;
            }
            else
            {
                if (is_integer($response->error))
                {
                    $error = $this->errors[$response->error];
                }
                else
                {
                    $error = $response->error;
                }

                $this->error($error); exit;
            }
        }
        catch (\Exception $e)
        {
            $this->error($e->getMessage()); exit;
        }
    }

    /**
     * Gelen veriden gerekli/gereksiz proxyleri tespit et.
     *
     * @var array $list
     * @return null
     */
    private function fill(array $list)
    {
        foreach ($list as $id => $proxy)
        {
            if ($proxy['version'] == $this->options['proxy.proxy_version'])
            {
                $this->all_proxies[] = $proxy;

                /**
                 * Ömrü 1 günden kısaysa süresi bitmiş veya biteceklere at.
                 */
                if ((new Carbon)->diffInDays($proxy['date_end']) <= 1)
                    $this->expiry_proxies[] = $proxy;
                else
                    $this->active_proxies[] = $proxy;
            }
        }

        $this->required_proxies = ($this->options['proxy.max_buy_piece'] - count($this->active_proxies));
    }

    /**
     * Gelen listeyi veritabanına ekle.
     *
     * @var array $list
     * @return null
     */
    private function db(array $list)
    {
        foreach ($list as $id => $proxy)
        {
            /**
             * Ömrü 1 günden uzunsa db kayıt.
             */
            if ((new Carbon)->diffInDays($proxy['date_end']) > 1)
            {
                Proxy::updateOrCreate(
                    [
                        'ip' => $proxy['host'],
                        'port' => $proxy['port'],
                        'username' => $proxy['user'],
                        'password' => $proxy['pass'],
                        'type' => $proxy['version'] == 6 ? 'ipv6' : ($proxy['version'] == 3 || $proxy['version'] == 4 ? 'ipv4' : 'ipv4'),
                    ],
                    [
                        'expiry_date' => (new DT)->nowAt($proxy['date_end']),
                    ]
                );
            }
            else
                $this->error('!db->expired!');
        }
    }
}
