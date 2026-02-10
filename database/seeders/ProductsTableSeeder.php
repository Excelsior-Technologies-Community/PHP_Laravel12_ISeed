<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('products')->delete();
        
        \DB::table('products')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Laptop',
                'price' => 50000,
                'description' => 'High-performance laptop',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Mobile',
                'price' => 20000,
                'description' => 'Smartphone with great camera',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Keyboard',
                'price' => 1000,
                'description' => 'Mechanical keyboard',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Mouse',
                'price' => 500,
                'description' => 'Wireless mouse',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Monitor',
                'price' => 15000,
                'description' => '27-inch 4K monitor',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Laptop',
                'price' => 50000,
                'description' => 'High-performance laptop',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Mobile',
                'price' => 20000,
                'description' => 'Smartphone with great camera',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Keyboard',
                'price' => 1000,
                'description' => 'Mechanical keyboard',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Mouse',
                'price' => 500,
                'description' => 'Wireless mouse',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Monitor',
                'price' => 15000,
                'description' => '27-inch 4K monitor',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}