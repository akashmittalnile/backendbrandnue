<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumanToInstructionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructionals', function (Blueprint $table) {
            $table->enum('location_type', array('Local', 'External'))->after('status')->default('Local');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructionals', function (Blueprint $table) {
            $table->dropColumn([
                'location_type'
            ]);
        });
    }
}
