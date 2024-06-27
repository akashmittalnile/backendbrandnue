<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->date('track_date');
            $table->string('current_day_weight')->nullable();
            $table->string('current_day_waist_measurement')->nullable();
            $table->string('water_intake')->nullable();
            $table->text('supplement')->nullable();
            $table->text('exercise')->nullable();
            $table->text('breakfast')->nullable();
            $table->text('lunch')->nullable();
            $table->text('snack')->nullable();
            $table->text('dinner')->nullable();
            $table->text('total_exercise_duration')->nullable();
            $table->time('fast_start_time')->nullable();
            $table->time('fast_end_time')->nullable();
            $table->double('total_fast_hour',[5,2])->nullable();
            $table->char('bowel_movement',2)->nullable();
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
        Schema::dropIfExists('trackings');
    }
}
