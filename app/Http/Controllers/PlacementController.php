<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;
use App\Models\LocationCheck;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\PythonController;

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
        $productID = $request->query('barcode');

        $product = Product::where('id', $productID)->first();
        if (!$product) {
            \Log::error('Product not found', ['product_id' => $productID]);
            return response()->json(['error' => 'Product not found.'], 400);
        }
        
        // $locationId = session('current_rack');
        // $location = Location::find($locationId);

        $location = Location::find('L0005');
        if (!$location) {
            return response()->json(["error" => "Rack location not found in session"], 404);
        }

        $correctLocation = Location::find($product->location_id);

        if ($location->id !== $correctLocation->id) {
            \Log::info('Product in wrong location', [
                'product_id' => $productID, 
                'wrong_location' => $location->id, 
                'correct_location' => $correctLocation->id
            ]);

            DB::table('placement_error_report')->insert([
                'product_id' => $productID,
                'wrong_location' => $location->id,
                'correct_location' => $correctLocation->id,
                'status' => 'Pending',
                'scan_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mail::to('maha1822003@gmail.com')->send(new PlacementErrorMail([
            //     'product' => $product,
            //     'wrong_location' => $location->id,
            //     'correct_location' => $correctLocation->id,
            // ]));

            // Fetch all admin emails
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();

            // Send email to all admins
            Mail::to($adminEmails)->send(new PlacementErrorMail([
                'product' => $product,
                'wrong_location' => $location->id,
                'correct_location' => $correctLocation->id,
            ]));

            //fix this: Fetch the error reports
            $errorReports = LocationCheck::where('product_id', $product->id)->get();

            // Store product details in session
            session()->put('product', [
                'product_id' => $product->id,
                'product_name' => $product->title,
                'product_quantity' => $product->quantity,
                'zone_name' => $correctLocation->zone_name,
                'rack' => $correctLocation->rack,
                'errorReports' => $errorReports->toArray(),
            ]);

            // Return the error reports as a JSON response
            return response()->json([
                'product' => session('product'),
                'success' => true,
                'error' => 'Wrong placement detected.',
                'location' => $location->id,
                'errorReports' => $errorReports,
                'locationCurrentcapacity' => $location->current_capacity,
                'locationCapacity' => $location->capacity,
                'locationzone' => $location->zone_name,
                'wrong_location' => $location->id,
                'correct_location' => $correctLocation->id,
            ]);
        }

        // Return product details even if placement is correct
        return response()->json([
            'product' => [
                'product_id' => $product->id,
                'product_name' => $product->title,
                'product_quantity' => $product->quantity,
                'zone_name' => $correctLocation->zone_name,
                'rack' => $correctLocation->rack,
            ],
            'success' => 'Correct placement.'
        ]);
    }
    


}
