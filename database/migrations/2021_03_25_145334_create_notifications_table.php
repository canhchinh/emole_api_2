<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateNotificationsTable.
 */
class CreateNotificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        \Illuminate\Support\Facades\Schema::rename('notify', 'notifications');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        \Illuminate\Support\Facades\Schema::rename('notifications', 'notify');
	}
}
