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
    protected $signature = 'crawler:twitter:organizer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Twitter keyword and follow organizer.';

    protected $keyword_chunk;
    protected $follow_chunk;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->keyword_chunk = 100;
        $this->profile_chunk = 2000;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = [];

        /**
         * Keywords
         */
        foreach ($this->getTracks('keyword') as $items)
        {
            $items = implode(',', $items);
            $data[] = [
                'tmp_key' => md5($items),
                'value' => $items,
                'type' => 'keyword',
            ];
        }

        /**
         * Profile
         */
        foreach ($this->getTracks('profile') as $items)
        {
            $items = implode(',', $items);
            $data[] = [
                'tmp_key' => md5($items),
                'value' => $items,
                'type' => 'follow',
            ];
        }

        $tokens = TwitterToken::whereNotIn('status', [ 'error' ])->get();

        $i = 0;
        $ids = [];

        foreach ($tokens as $token)
        {
            $this->line('----------------------');

            if ($__ = @$data[$i])
            {
                $ids[] = $token->id;

                $this->line("-- $token->screen_name ($token->device)");
                $this->line('-- '.$__['value']);

                if ($token->tmp_key == $__['tmp_key'] && $token->status == 'working')
                {
                    if ($token->updated_at >= (new DT)->nowAt('-5 minutes'))
                    {
                        $token->update(
                            [
                                'status' => 'run'
                            ]
                        );

                        $this->info('-- The working token has not been transacted for a long time.');
                    }
                    else
                        $this->info('-- Token is working');
                }
                else
                {
                    $token->update(
                        [
                            'status' => 'restart',
                            'tmp_key' => $__['tmp_key'],
                            'value' => $__['value'],
                            'type' => $__['type'],
                            'error_hit' => 0
                        ]
                    );

                    $this->info('-- Token updated');
                }
            }
            else
            {
                $this->info('-- All follow-ups have been compiled.');
                $this->line('----------------------');

                $break = true;
                break;
            }

            $i++;

            $this->line('----------------------');
        }

        if (@$break)
        {
            $this->info('Break!');
        }
        else
        {
            $this->error('Not enough tokens for tracking!');

            Notification::send(
                User::where('is_root', true)->get(),
                (new ServerAlert('Twitter tokens are not enough. Please connect a new account to the system.'))->onQueue('notifications')
            );
        }

        TwitterToken::where('status', '<>', 'error')
            ->whereNotIn('id', $ids)->update(
            [
                'pid' => null,
                'status' => 'close',
                'type' => null,
                'tmp_key' => null,
                'value' => null,
                'error_hit' => 0,
                'error_reason' => null,
            ]
        );
    }

    private function getTracks(string $type)
    {
        $query = Track::whereHas('user', function($q) {
                $q->whereDate('users.subscription_end_date', '>=', (new DT)->nowAt());
            })
            ->where('type', $type)
            ->whereNull('error_reason')
            ->where(function($query) {
                $query->orWhere('valid', true);
                $query->orWhereNull('valid');
            })
            ->where('source', 'twitter')
            ->orderBy('id', 'asc');

        $query->update([ 'valid' => true ]);

        $chunk = $query->pluck('value')
            ->chunk($this->{$type.'_chunk'})
            ->toArray();
        $chunk = array_values($chunk);
        $chunk = array_map(function($item) {
            return str_replace(
                [
                    'https',
                    'http',
                    ':',
                    '/',
                    'twitter.com'
                ],
                '',
                $item
            );
        }, $chunk);

        return $chunk;
    }
}
