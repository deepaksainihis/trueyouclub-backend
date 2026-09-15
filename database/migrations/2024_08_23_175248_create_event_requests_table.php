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
        Schema::create('event_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');          
            $table->unsignedBigInteger('volunteer_id');          
            $table->longText('custom_message')->nullable()->default(null); 
            $table->integer('attempts')->nullable()->default(null); 
            $table->tinyInteger('status')->default(0)->comment('0=> pending, 1=> accepted,2=> declined');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('volunteer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_requests');
    }
};
