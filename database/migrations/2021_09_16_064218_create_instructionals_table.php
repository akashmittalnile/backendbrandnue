<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstructionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructionals', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('url');
            $table->string('type');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('subscription_plan_id')->nullable();
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('set null');
            $table->char('status',1)->default(config('constant.status.in_active'));
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
        Schema::dropIfExists('instructionals');
    }
}
