<?php

use Illuminate\Database\Seeder;
use App\Models\Supplement;

class SupplementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplement::insert([
            ['name'=>'Multi Vitamin','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Vitamin C','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Vitamin D','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Zinc','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Citrulline Malate','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],
            ['name'=>'Probiotic','status'=>config('constant.status.active'),'created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]
        ]);
    }
}
