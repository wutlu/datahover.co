<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstagramAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_accounts', function (Blueprint $table) {
            $table->id()->unsigned();

            $table->enum('status', [ 'normal', 'authenticate', 'error' ])->default('authenticate');

            $table->string('email')->unique();
            $table->string('password');

            $table->string('proxy')->nullable()->default(null);
            $table->string('user_agent')->nullable()->default(null);
            $table->string('sessionid');

            $table->unsignedSmallInteger('error_hit')->default(0);
            $table->string('error_reason')->nullable()->default(null);

            $table->unsignedBigInteger('request_hit')->default(0);
            $table->timestamp('request_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('instagram_accounts');
    }
}
