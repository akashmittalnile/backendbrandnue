<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribeMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
            $table->string('square_payment_subscription_id')->nullable();
            $table->date('activated_date');
            $table->date('renewal_date');
            $table->date('modify_date')->nullable();
            $table->enum('status',['Active','Cancelled','Upgraded','Pending'])->default('Pending');
            $table->enum('activated_from',['Admin','Online'])->nullable();
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
        Schema::dropIfExists('subscribe_members');
    }
}
