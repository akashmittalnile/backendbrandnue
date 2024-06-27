<?php

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Exercise::insert([
            ['name'=>'Walking','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Interval training','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Squats','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Lunges','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Push-ups','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Abdominal Crunches','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Bent-over Row','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]
        ]);
    }
}
