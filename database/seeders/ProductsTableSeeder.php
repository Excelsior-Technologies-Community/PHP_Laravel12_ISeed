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
                'category' => 'Electronics',
                'price' => 50000,
                'description' => 'High-performance laptop',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Mobile',
                'category' => 'Electronics',
                'price' => 20000,
                'description' => 'Smartphone with great camera',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Keyboard',
                'category' => 'Electronics',
                'price' => 1000,
                'description' => 'Mechanical keyboard',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Mouse',
                'category' => 'Electronics',
                'price' => 500,
                'description' => 'Wireless mouse',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Monitor',
                'category' => 'Electronics',
                'price' => 15000,
                'description' => '27-inch 4K monitor',
                'status' => 'inactive',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'T-Shirt',
                'category' => 'Clothing',
                'price' => 800,
                'description' => 'Cotton casual t-shirt',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Book',
                'category' => 'Books',
                'price' => 500,
                'description' => 'Laravel programming book',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Football',
                'category' => 'Sports',
                'price' => 1200,
                'description' => 'Professional football',
                'status' => 'inactive',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Chair',
                'category' => 'Furniture',
                'price' => 3500,
                'description' => 'Comfortable office chair',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Headphones',
                'category' => 'Electronics',
                'price' => 2500,
                'description' => 'Noise cancellation headphones',
                'status' => 'active',
                'created_at' => '2026-08-06 10:02:37',
                'updated_at' => '2026-08-06 10:02:37',
            ),
        ));
        
        
    }
}