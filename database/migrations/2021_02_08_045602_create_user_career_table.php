<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCareerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_careers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('career_id')->nullable();
            $table->text('tags')->nullable();
            $table->text('setting')->nullable();
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
        Schema::dropIfExists('user_careers');
    }
}
