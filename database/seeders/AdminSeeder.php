<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admins')->insert([
            'username' => "phancanhchinh",
            'email' => 'phancanhchinh@gmail.com',
            'password' => bcrypt("Chinh@123"),
        ]);
    }
}