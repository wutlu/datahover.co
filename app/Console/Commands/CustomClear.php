<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\DataPool;

use Etsetra\Library\DateTime as DT;

class CustomClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes old data';

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
        $query = (new DataPool)->deleteByQuery(
            [
                'bool' => [
                    'must' => [
                        [ 'match' => [ 'site' => 'foxnews.com' ] ],
                        [ 'match' => [ 'status' => 'err' ] ],
                    ]
                ]
            ]
        );

        print_r($query);
    }
}
