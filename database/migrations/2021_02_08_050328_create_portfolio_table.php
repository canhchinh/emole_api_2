<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('work_title')->nullable();
            $table->string('image_url', 2000)->nullable();
            $table->text('work_description')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_still_active')->nullable();
            $table->string('member')->nullable();
            $table->float('budget')->nullable();
            $table->float('reach_number')->nullable();
            $table->integer('view_count')->nullable();
            $table->integer('like_count')->nullable();
            $table->integer('comment_count')->nullable();
            $table->integer('cpa_count')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('work_link')->nullable();
            $table->text('work_tag')->nullable();
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
        Schema::dropIfExists('portfolio');
    }
}
