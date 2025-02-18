<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Path to your CSV file
        $csvFile = database_path('seeders/products.csv');

        // Read the CSV file
        $csvData = array_map('str_getcsv', file($csvFile));

        // Skip the header row
        array_shift($csvData);

        // Insert data into the products table
        foreach ($csvData as $row) {
            DB::table('products')->insert([
                'id' => $row[0],
                'title' => $row[1],
                'description' => $row[2],
                'main_category' => $row[3],
                'quantity' => (int)$row[4],
                'location_id' => $row[5] ?: null, // Set to null if empty
                'barcode_path' => $row[6],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
