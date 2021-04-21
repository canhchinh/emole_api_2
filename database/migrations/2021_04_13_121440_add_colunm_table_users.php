<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColunmTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string("facebook_user")->nullable()->after("youtube_channel");
            $table->string("line_user")->nullable()->after("facebook_user");
            $table->string("note_user")->nullable()->after("line_user");
            $table->string("pinterest_user")->nullable()->after("note_user");
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
            $table->dropColumn("facebook_user");
            $table->dropColumn("line_user");
            $table->dropColumn("note_user");
            $table->dropColumn("pinterest_user");
        });
    }
}