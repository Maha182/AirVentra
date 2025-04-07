<?php 

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;
use League\Csv\Reader;

class ProductBatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table to remove any existing data
        DB::table('product_batches')->truncate();
    
        // Path to your CSV file
        $csvFile = database_path('seeders/product_batches.csv');  // Ensure this is the correct path
    
        // Load the CSV file
        $csv = Reader::createFromPath($csvFile, 'r');
        $csv->setHeaderOffset(0); // Set the first row as header
        $records = $csv->getRecords(); // Get all the records from the CSV
    
        foreach ($records as $record) {
            DB::table('product_batches')->insert([
                'product_id' => $record['product_id'],
                'barcode' => $record['barcode'],
                'quantity' => $record['quantity'],
                'expiry_date' => $record['expiry_date'],
                'received_date' => $record['received_date'],
                'location_id' => $record['location_id'],
                'status' => $record['status'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
    
}

