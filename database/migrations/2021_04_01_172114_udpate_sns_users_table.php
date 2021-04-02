<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UdpateSnsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('twitter_user')->nullable(true);
            $table->string('tiktok_user')->nullable(true);
            $table->string('instagram_user')->nullable(true);
            $table->string('youtube_channel')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('twitter_user');
            $table->dropColumn('tiktok_user');
            $table->dropColumn('instagram_user');
            $table->dropColumn('youtube_channel');
        });
    }
}
