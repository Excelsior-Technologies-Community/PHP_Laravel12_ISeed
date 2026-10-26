<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'user1@example.test',
                'email_verified_at' => NULL,
                'password' => '$2y$12$e8hGFmE4/m1k5tB6V9zW3e1dE.w8vF0wKjM.9V0vU8X7m2qY3o6tC',
                'remember_token' => 'test_token_sample',
                'created_at' => '2026-10-26 03:58:17',
                'updated_at' => '2026-10-26 03:58:17',
            ),
        ));
        
        
    }
}