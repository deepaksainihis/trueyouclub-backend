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
        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('habit_id');
            $table->unsignedBigInteger('user_id');
            $table->date('log_date');
            $table->enum('status', ['completed', 'skipped'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('habit_id')->references('id')->on('habits')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['habit_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('habit_logs');
    }
};
