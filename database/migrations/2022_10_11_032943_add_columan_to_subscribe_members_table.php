<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumanToSubscribeMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribe_members', function (Blueprint $table) {
            $table->unsignedBigInteger('request_plan_id')->nullable()->after('subscription_plan_id');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribe_members', function (Blueprint $table) {
            $table->dropColumn([
                'request_plan_id'
            ]);
        });
    }
}
