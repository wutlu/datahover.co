<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStatusColumnToTwitterTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twitter_tokens', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('twitter_tokens', function (Blueprint $table) {
            $table->enum(
                'status',
                [
                    'working',
                    'not_working',

                    'start',
                    'stop',
                    'restart',
                    'error',
                ]
            )
            ->default('not_working');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('twitter_tokens', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('twitter_tokens', function (Blueprint $table) {
            $table->enum('status', [ 'working', 'close', 'error', 'restart', 'kill', 'run' ])->default('close');
        });
    }
}
