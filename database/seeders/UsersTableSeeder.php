<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {

        $users = [
            [
                'first_name'     => 'Super',
                'last_name'      => 'Admin',
                'name'           => 'Super Admin',
                'email'          => 'dev@gmail.com',
                'password'       => bcrypt('dev@123#'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],      
            [
                'first_name'     => 'Super',
                'last_name'      => 'Admin',
                'name'           => 'Super Admin',
                'email'          => 'xy.stellasong@gmail.com',
                'password'       => bcrypt('B8Az4U@!Kk'),
                'remember_token' => null,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],

        ];
        User::insert($users);
    }
}
