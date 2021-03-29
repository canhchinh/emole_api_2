<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateUserNotificationsTable.
 */
class CreateUserNotificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		\Illuminate\Support\Facades\Schema::create('user_notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->default(0);
            $table->text('notification_data')->nullable();
            $table->timestamps();
		});

        \Illuminate\Support\Facades\Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('career_id');
            $table->text('career_ids')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        \Illuminate\Support\Facades\Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('career_ids');
            $table->integer('career_id')->nullable()->default(0);
        });
        \Illuminate\Support\Facades\Schema::drop('user_notifications');
	}
}
