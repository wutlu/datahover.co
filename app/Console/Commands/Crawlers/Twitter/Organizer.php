<?php

namespace App\Console\Commands\Crawlers\Twitter;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

use App\Models\Track;
use App\Models\TwitterToken;
use App\Models\User;
use App\Models\Option;

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

    protected $chunk_count = 25;

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
        $tracks = Track::where(function($query) {
                $query->orWhere('valid', true);
                $query->orWhereNull('valid');
            })
            ->where(
                [
                    'source' => 'twitter',
                    'type' => 'keyword'
                ]
            )
            ->orderBy('id', 'asc')
            ->get();

        $array = [];

        if (count($tracks))
        {
            foreach ($tracks as $track)
            {
                if ($track->subscriptionEndDate() >= date('Y-m-d'))
                {
                    $array[] = $track->value;

                    $track->update([ 'valid' => true ]);
                }
                else
                    $this->error('This track does not belong to a current account.');
            }
        }

        /***/

        $chunks = array_values(array_chunk($array, $this->chunk_count));
        $total_chunk = count($chunks);
        $this->info($total_chunk.' chunk detected.');

        $tokens = TwitterToken::orderBy('id', 'desc')->get()->toArray();
        $total_token = count($tokens);
        $this->info("There are $total_token tokens.");

        $needed_token = $total_chunk - $total_token;

        if ($needed_token > 0)
        {
            $this->newLine();

            $this->error("Need $needed_token Twitter token.");

            Notification::send(
                User::where('is_root', true)->get(),
                (new ServerAlert("Need $needed_token Twitter token."))->onQueue('notifications')
            );

            $this->newLine();
        }

        /***/

        $ids = [];

        foreach ($chunks as $key => $chunk)
        {
            $this->newLine();

            $value = implode(',', $chunk);
            $this->info("Values: $value");

            $tmp_key = md5($value);
            $this->line($tmp_key);

            if ($token = @$tokens[$key])
            {
                $ids[] = $token['id'];

                $this->line('----');
                $this->info('Assigned Token: '.$token['screen_name'].' ('.$token['device'].')');

                if ($token['tmp_key'] == $tmp_key)
                    $this->line('Token stable');
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

            $this->newLine();
        }

        TwitterToken::whereNotIn('id', $ids)
            ->whereNotIn('status', [ 'error', 'not_working' ])
            ->update([ 'status' => 'stop' ]);
    }
}
