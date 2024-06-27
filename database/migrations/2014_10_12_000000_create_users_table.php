<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
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
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('gender',['Male','Female','Others'])->nullable();
            $table->date('dob')->nullable();
            $table->string('height_feet')->nullable();
            $table->string('height_inch')->nullable();
            $table->string('waist_measurement')->nullable();
            $table->string('today_waist_measurement')->nullable();
            $table->string('goal_waist_measurement')->nullable();
            $table->string('current_weight')->nullable();
            $table->string('today_current_weight')->nullable();
            $table->string('goal_weight')->nullable();
            $table->string('profile_image')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->char('status')->default(config('constant.status.in_active'));
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('fcm_token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
