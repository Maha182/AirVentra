<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;
use League\Csv\Reader;

class ProductBatchSeeder extends Seeder
{
   
    public function run()
    {
        // Truncate the table to remove any existing data
    
        $csvFile = database_path('seeders/product_batches.csv');

        // Read the CSV file
        $csvData = array_map('str_getcsv', file($csvFile));

        // Skip header row
        array_shift($csvData);

        foreach ($csvData as $row) {
            DB::table('product_batches')->insert([
                'product_id'    => $row[0],
                'barcode'       => $row[1],
                'quantity'      => (int) $row[2],
                'expiry_date'   => $row[3] !== '' ? $row[3] : null,
                'received_date' => $row[4],
                'location_id'   => $row[5] !== '' ? $row[5] : null,
                'status'        => $row[6] ?? 'in_stock',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
    
}

