<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();
            $table->string('name');
            $table->string('avatar')->nullable()->default(null);
            $table->string('email')->unique();
            $table->string('timezone')->default('UTC');

            $table->string('subscription')->default('trial');
            $table->timestamp('subscription_end_date')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('stripe_id')->nullable()->index();

            $table->string('api_key');
            $table->string('api_secret');
            $table->index([ 'api_key', 'api_secret' ]);

            $table->boolean('is_root')->default(false);

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
