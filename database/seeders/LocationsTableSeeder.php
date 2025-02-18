<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Path to your CSV file
        $csvFile = database_path('seeders/locations.csv');

        // Read the CSV file
        $csvData = array_map('str_getcsv', file($csvFile));

        // Skip the header row
        array_shift($csvData);

        // Insert data into the locations table
        foreach ($csvData as $row) {
            DB::table('locations')->insert([
                'id' => $row[0],
                'zone_name' => $row[1],
                'aisle' => $row[2],
                'rack' => $row[3],
                'capacity' => (int)$row[4],
                'current_capacity' => (int)$row[5],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
