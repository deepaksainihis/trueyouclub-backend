<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_contents', function (Blueprint $table) {
            $table->id();
            $table->date('scheduled_date');
            $table->enum('type', ['micro_learning', 'audio', 'video']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('media_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_contents');
    }
};
