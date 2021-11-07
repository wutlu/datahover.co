<?php

use Illuminate\Database\Migrations\Migration;

use App\Models\DataPool as Model;

class CreateDataPoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings = config('elasticsearch.settings');
        $settings['number_of_shards'] = 1;
        $settings['number_of_replicas'] = 0;

        $query = (new Model)->createIndex(
            [
                'properties' => [
                    'id' => [ 'type' => 'keyword' ],

                    //

                    'created_at' => [ 'type' => 'date' ]
                ]
            ],
            $settings
        );

        print_r($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $query = (new Model)->deleteIndex();

        print_r($query);
    }
}
