<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id()->unsigned();

            $table->json('users')->default('[]');

            $table->string('source')->index(); // facebook.com, twitter.com, nytimes.com
            $table->string('type')->index(); // keyword, user, content, location
            $table->string('value')->index(); // profile url, keyword, content url, location

            $table->unique([ 'source', 'type', 'value' ]);

            $table->unsignedSmallInteger('error_hit')->default(0);
            $table->string('error_reason')->nullable()->default(null);

            $table->unsignedInteger('total_data')->default(0);

            $table->unsignedBigInteger('request_hit')->default(0);
            $table->unsignedSmallInteger('request_frequency')->default(5);
            $table->timestamp('request_at')->default(\DB::raw('CURRENT_TIMESTAMP'));

            $table->boolean('valid')->nullable()->default(null);

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
        Schema::dropIfExists('tracks');
    }
}
