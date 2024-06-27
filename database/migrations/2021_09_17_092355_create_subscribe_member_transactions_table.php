<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribeMemberTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe_member_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscribe_member_id');
            $table->foreign('subscribe_member_id')->references('id')->on('subscribe_members')->onDelete('cascade');
            $table->string('payment_status');
            $table->double('price',['10,2'])->default(0);
            $table->text('data')->nullable();
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
        Schema::dropIfExists('subscribe_member_transactions');
    }
}
