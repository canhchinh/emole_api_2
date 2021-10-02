<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableUserWork extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_works', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('work_id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('make_up')->nullable();
            $table->boolean('transportation_cost')->nullable();
            $table->boolean('post_media')->nullable();
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
        Schema::dropIfExists('user_works');
    }
}