<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\DataPool;

use Etsetra\Library\DateTime as DT;

class DataClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear';

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
                        [ 'match' => [ 'status' => 'ok' ] ]
                    ],
                    'filter' => [
                        [
                            'range' => [ 'called_at' => [ 'lte' => (new DT)->nowAt('-30 days') ] ]
                        ]
                    ]
                ]
            ]
        );

        print_r($query);
    }
}
