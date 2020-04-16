<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Superadmin',
                'email' => 'admin@laravel.com',
                'password' => Hash::make('12345678'),
                'role_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe@laravel.com',
                'password' => Hash::make('12345678'),
                'role_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DB::table('users')->insert($users);
    }
}
