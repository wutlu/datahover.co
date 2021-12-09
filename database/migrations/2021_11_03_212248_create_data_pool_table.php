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
                    /*
                     * status;
                     * - buffer: bağlantı yeni alındı,
                     * - call: bağlantıya istek gönderildi,
                     * - ok: geçerli bir içerik,
                     * - err: içerik alınamadı,
                     */
                    'status' => [ 'type' => 'keyword' ],

                    'id' => [ 'type' => 'keyword' ],

                    'site' => [ 'type' => 'keyword' ],
                    'link' => [ 'type' => 'keyword', 'index' => false ],
                    'title' => [ 'type' => 'text', 'analyzer' => 'custom' ],
                    'text' => [ 'type' => 'text', 'analyzer' => 'custom' ],
                    'lang' => [ 'type' => 'keyword' ],
                    'device' => [ 'type' => 'keyword' ],
                    'image' => [ 'type' => 'keyword', 'index' => false ],

                    'user' => [
                        'properties' => [
                            'id' => [ 'type' => 'keyword' ],
                            'name' => [ 'type' => 'keyword', 'normalizer' => 'keyword_normalizer' ],
                            'title' => [ 'type' => 'keyword', 'normalizer' => 'keyword_normalizer' ],
                            'image' => [ 'type' => 'keyword', 'index' => false ],
                            'description' => [ 'type' => 'text', 'analyzer' => 'custom' ],
                        ]
                    ],

                    'created_at' => [ 'type' => 'date' ],
                    'called_at' => [ 'type' => 'date' ],

                    'log' => [ 'type' => 'text', 'index' => false ],
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
