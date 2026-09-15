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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_customer_id')->nullable();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name');
            $table->string('email')->nullable()->default(null);
            $table->timestamp('email_verified_at')->nullable();

            $table->string('phone')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            $table->string('password')->nullable()->default(null);

            $table->tinyInteger('register_type')->default(1)->comment('1=> Register page, 2 => Google, 3 => Facebook');
            $table->string('social_id')->nullable()->default(null);
            $table->json('social_json')->nullable()->default(null);

            $table->rememberToken();
            $table->boolean('is_active')->default(1)->comment('1 => break, 0 => continue');
            $table->datetime('last_login_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};
