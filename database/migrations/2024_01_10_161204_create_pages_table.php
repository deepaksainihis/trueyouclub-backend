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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_page_id')->default(null)->nullable();
            $table->text('page_key')->default(null)->nullable();
            $table->string('title', 191)->default(null)->nullable();
            $table->string('slug', 191)->default(null)->nullable();
            $table->string('type')->default('header')->comment('1=> support menu, 2=> useful links, 3=> header');
            $table->text('subtitle')->default(null)->nullable();
            $table->json('button')->default(null)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0=> inactive, 1=> active');
            $table->unsignedBigInteger('created_by');
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
        Schema::dropIfExists('pages');
    }
};
