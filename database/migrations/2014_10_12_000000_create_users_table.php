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
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('given_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('title')->nullable();
            $table->string('profession')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->text('self_introduction')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('register_finish_step')->default(0);
            $table->integer('activity_base_id')->nullable();
            $table->boolean('is_enable_email_notification')->nullable();
            $table->jsonb('category_ids')->nullable();
            $table->jsonb('job_ids')->nullable();
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
