<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_tokens', function (Blueprint $table) {
            $table->id()->unsigned();

            $table->enum('status', [ 'working', 'close', 'error', 'restart', 'kill', 'run' ])->default('close');

            $table->string('screen_name')->index();
            $table->string('password');

            $table->string('device');
            $table->string('consumer_key');
            $table->string('consumer_secret');
            $table->string('access_token');
            $table->string('access_token_secret');

            $table->enum('type', [ 'keyword', 'follow' ])->nullable()->default(null);

            $table->string('tmp_key')->nullable()->default(null);
            $table->longText('value')->nullable()->default(null);

            $table->unsignedSmallInteger('error_hit')->default(0);
            $table->string('error_reason')->nullable()->default(null);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_tokens');
    }
}
