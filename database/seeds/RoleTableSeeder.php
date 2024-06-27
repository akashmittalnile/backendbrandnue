<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => config('constant.role.admin'),
            'display_name' => 'Admin',
            'description' => 'Admin of this blog'
        ]);

        Role::create([
            'name' => config('constant.role.customer'),
            'display_name' => 'Customer',
            'description' => 'Customer of this blog'
        ]);
    }
}
