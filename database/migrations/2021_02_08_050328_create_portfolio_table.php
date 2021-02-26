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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('title')->nullable();
            $table->string('image', 2000)->nullable();
            $table->text('job_description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_still_active')->nullable();
            $table->string('member')->nullable();
            $table->string('budget')->nullable();
            $table->string('reach_number')->nullable();
            $table->string('view_count')->nullable();
            $table->string('like_count')->nullable();
            $table->string('comment_count')->nullable();
            $table->string('cpa_count')->nullable();
            $table->string('video_link')->nullable();
            $table->string('work_link')->nullable();
            $table->text('work_description')->nullable();
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
        Schema::dropIfExists('portfolios');
    }
}