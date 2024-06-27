<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create(
            [
                'name' => 'Admin',
                'email' => 'admin@niletechnologies.com',
                'password' => bcrypt('admin@123'),
                'status' => config('constant.status.active')
            ]
        );

        $adminRole = Role::where('name', 'ADMIN')->first();
        $admin->roles()->attach($adminRole);

        /*$customer = User::create(
            [
                'name' => 'Customer',
                'email' => 'customer@niletechnologies.com',
                'password' => bcrypt('abc@123'),
                'status' => config('constant.status.active')
            ]
        );

        $customerRole = Role::where('name', 'CUSTOMER')->first();
        $customer->roles()->attach($customerRole);*/
    }
}
