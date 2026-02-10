<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Call the ProductsTableSeeder
        $this->call(ProductsTableSeeder::class);
        
        // You can add more seeders here
        // $this->call(UsersTableSeeder::class);
    }
}