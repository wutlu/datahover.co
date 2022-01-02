<?php

use Illuminate\Database\Migrations\Migration;

use App\Models\DataPool as Model;

class PutSettingsMaxResultWindowToDataPoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = (new Model)->putIndexSettings(
            [
                'index.max_result_window' => 100000
            ]
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
        $query = (new Model)->putIndexSettings(
            [
                'index.max_result_window' => 10000
            ]
        );

        print_r($query);
    }
}
