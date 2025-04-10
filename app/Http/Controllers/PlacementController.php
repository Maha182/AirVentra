<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;
use App\Models\LocationCheck;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\StorageAssignmentController;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlacementErrorMail;

class PlacementController extends Controller
{
    public function getBarcode()
    {
        $client = new Client();
        $response = $client->get('http://127.0.0.1:5000/get_barcode');
        $data = json_decode($response->getBody()->getContents(), true);
        
        return response()->json(['barcode' => $data['barcode'] ?? null]);
    }

    
    public function checkPlacement(Request $request)
    {
        $barcode = $request->query('barcode');

        // Find the batch by its barcode
        $batch = DB::table('product_batches')->where('barcode', $barcode)->first();

        if (!$batch) {
            \Log::error('Batch not found for scanned barcode', ['barcode' => $barcode]);
            return response()->json(['error' => 'Batch not found.'], 400);
        }

        // Get product info
        $product = DB::table('products')->where('id', $batch->product_id)->first();
        if (!$product) {
            \Log::error('Product not found for batch', ['product_id' => $batch->product_id]);
            return response()->json(['error' => 'Product not found.'], 400);
        }

        $locationData = session('current_rack');  // Get the full session data
        $locationId = $locationData['rack_id'];   // Extract just the rack_id

        $scannedLocation = Location::find($locationId);  // Find the location by the rack_id


        if (!$scannedLocation) {
            return response()->json(["error" => "Rack location not found in session"], 404);
        }

        // Check if batch is in correct location
        $correctLocation = Location::find($batch->location_id);

        if ($scannedLocation->id !== $batch->location_id) {
            \Log::info('Batch in wrong location', [
                'barcode' => $barcode,
                'batch_id' => $batch->id,
                'wrong_location' => $scannedLocation->id,
                'correct_location' => $batch->location_id,
            ]);

            // Insert into placement error report
            $errorId = DB::table('placement_error_report')->insertGetId([
                'product_id' => $product->id,
                'batch_id' => $batch->id,
                'barcode' => $barcode,
                'wrong_location' => $scannedLocation->id,
                'correct_location' => $batch->location_id,
                'status' => 'Pending',
                'scan_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign task to employee
            $taskController = new TaskAssignmentController();
            $assignedEmployee = $taskController->assignTask($errorId);

            // Email notification
            if ($assignedEmployee) {
                Mail::to($assignedEmployee->email)->send(new PlacementErrorMail([
                    'product' => $product,
                    'barcode' => $barcode,
                    'wrong_location' => $scannedLocation->id,
                    'correct_location' => $batch->location_id,
                ]));
            }

            // Return response
            return response()->json([
                'product' => [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'batch_id' => $batch->id,
                    'barcode' => $barcode,
                    'quantity' => $batch->quantity,
                ],
                'success' => true,
                'error' => 'Wrong placement detected.',
                'location' => $scannedLocation->id,
                'correct_location' => $batch->location_id,
            ]);
        }

        // Correct placement
        return response()->json([
            'product' => [
                'product_id' => $product->id,
                'product_name' => $product->title,
                'batch_id' => $batch->id,
                'barcode' => $barcode,
                'quantity' => $batch->quantity,
            ],
            'success' => 'Correct placement.'
        ]);
    }

    


}
