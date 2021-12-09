<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->ipAddress('ip')->index();
            $table->unsignedInteger('port')->default(8080);
            $table->string('username')->nullable()->default(null);
            $table->string('password')->nullable()->default(null);
            $table->enum('type', [ 'ipv4', 'ipv6' ])->default('ipv4');
            $table->unsignedSmallInteger('speed')->default(0);
            $table->timestamp('expiry_date')->nullable()->default(null);
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
        Schema::dropIfExists('proxies');
    }
}
