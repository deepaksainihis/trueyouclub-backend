<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('page_id');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');

            $table->string('title')->nullable();
            $table->string('section_key')->nullable();
            $table->longtext('content_text')->nullable();
            $table->longtext('other_details')->nullable();
            $table->tinyInteger('is_image')->default(0)->comment('0=> No, 1=> yes');
            $table->tinyInteger('is_multiple_image')->default(0)->comment('0=> No, 1=> yes');
            $table->tinyInteger('is_video')->default(0)->comment('0=> No, 1=> yes');
            $table->json('button')->default(null)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=> inactive, 1=> active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
};
