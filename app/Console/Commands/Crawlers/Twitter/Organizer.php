<?php

namespace App\Console\Commands\Crawlers\Twitter;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use App\Models\Track;
use App\Models\TwitterToken;
use App\Models\User;
use App\Notifications\ServerAlert;

use Etsetra\Library\DateTime as DT;

class Organizer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitter:organizer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Twitter keyword and follow organizer.';

    protected $chunk_count = 50;

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
        $query = Track::whereHas('user', function($q) {
                $q->whereDate('users.subscription_end_date', '>=', (new DT)->nowAt());
            })
            ->where(function($query) {
                $query->orWhere('valid', true);
                $query->orWhereNull('valid');
            })
            ->where('source', 'twitter')
            ->where('type', 'keyword')
            ->orderBy('id', 'asc');

        $query->update([ 'valid' => true ]);

        $chunks = array_values($query->pluck('value')->unique()->chunk($this->chunk_count)->toArray());

        $tokens = TwitterToken::orderBy('id', 'desc')->get()->toArray();

        $total_chunk = count($chunks);
        $total_token = count($tokens);

        $this->info($total_chunk.' chunk detected.');
        $this->info($total_token.' token detected.');

        $needed_token = $total_chunk - $total_token;

        if ($needed_token > 0)
        {
            echo PHP_EOL;
            $this->error("Need $needed_token Twitter token.");

            Notification::send(
                User::where('is_root', true)->get(),
                (new ServerAlert("Need $needed_token Twitter token."))->onQueue('notifications')
            );
            echo PHP_EOL;
        }

        $ids = [];

        foreach ($chunks as $key => $chunk)
        {
            echo PHP_EOL;

            $value = implode(',', $chunk);
            $tmp_key = md5($value);

            $this->info($tmp_key);
            $this->info("Values: $value");

            if ($token = @$tokens[$key])
            {
                $ids[] = $token['id'];

                $this->line('----');
                $this->info('Assigned Token: '.$token['screen_name'].' ('.$token['device'].')');

                if ($token['tmp_key'] == $tmp_key)
                    $this->info('Token stable');
                else
                {
                    $this->line('----');

                    $this->line('Old Value: '.$token['value']);
                    $this->info('New Value: '.$value);

                    $this->line('----');

                    TwitterToken::find($token['id'])
                        ->update(
                            [
                                'value' => $value,
                                'tmp_key' => $tmp_key,
                                'status' => 'restart'
                            ]
                        );

                    $this->info('Token updated');
                }
            }
            else
                $this->error('Token not found');

            echo PHP_EOL;
        }

        TwitterToken::whereNotIn('status', [ 'error', 'kill', 'close' ])
            ->whereNotIn('id', $ids)
            ->update(
                [
                    'status' => 'kill',
                ]
            );
    }
}
