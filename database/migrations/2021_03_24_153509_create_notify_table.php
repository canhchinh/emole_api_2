<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify', function (Blueprint $table) {
            $table->id();
            $table->integer('career_id')->nullable()->default(0);
            $table->string('delivery_name')->nullable();
            $table->text('delivery_contents')->nullable();
            $table->string('subject')->nullable();
            $table->string('url')->nullable();
            $table->string('status', 30)->default(\App\Entities\Notify::STATUS_DRAFT);
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
        Schema::dropIfExists('notify');
    }
}
