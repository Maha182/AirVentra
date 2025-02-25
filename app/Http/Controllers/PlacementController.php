<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;
use App\Models\LocationCheck;
use Illuminate\Support\Facades\DB;

use App\Models\PlacementErrorReport;
use App\Http\Controllers\PythonController;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlacementErrorMail;

class PlacementController extends Controller
{
    
    public function checkPlacement(Request $request)
    {
        $client = new Client();

        // Fetch barcode from Python
        $barcodeResponse = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcodeResponse->getBody()->getContents(), true);
        $productID = $barcodeData['barcode'] ?? null;


        // Fetch product from the database
        $product = Product::where('id', $productID)->first();

        if (!$product) {
            \Log::error('Product not found in database', ['product_id' => $productID]);
            return response()->json(['error' => 'Product not found.'], 400);
        }

        // Fetch the fixed location from the database
        $location = Location::find('L0005');
        // $locationCurrentcapacity = $location-> current_capacity;
        // $locationCapacity = $location-> capacity;
        // $locationzone = $location-> zone_name;


        // Correct location for the product
        $correctLocation = Location::find($product->location_id);

        if ($location->id === $correctLocation->id) {
            // Mark the barcode as processed
            return response()->json(['success' => 'The product is in the correct place.']);
        } else {
            \Log::info('Product in wrong location', ['product_id' => $productID, 'wrong_location' =>  $location->id, 'correct_location' => $correctLocation->id]);

            // Log the error in placement_error table
            DB::table('placement_error_report')->insert([
                'product_id' => $productID,
                'wrong_location' => $location->id,
                'correct_location' => $correctLocation->id,
                'status' => 'Pending',
                'scan_date' => now(), // Ensure scan_date is included
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        

            // Send an email notification
            $emailData = [
                'product' => $product,
                'wrong_location' => $location->id,
                'correct_location' =>$correctLocation->id ,
            ];

            Mail::to('maha1822003@gmail.com')->send(new PlacementErrorMail($emailData));


            // Mark the barcode as processed
            $errorReports = PlacementErrorReport::all();


            session()->put('product', [
                'product_id' => $product->id,
                'product_name' => $product->title,
                'product_quantity' => $product->quantity,
                'zone_name' => $correctLocation->zone_name,
                'rack' => $correctLocation->rack,
            ]);
            // Return the error reports as a JSON response
            return response()->json([
                'product' => session('product'),
                'success' => true,
                'error' => $errorReports,
                'location' => $location->id,
                'locationCurrentcapacity' => $location->current_capacity,
                'locationCapacity' => $location->capacity,
                'locationzone' => $location->zone_name,
                'wrong_location' => $location->id,
                'correct_location' => $correctLocation->id,
            ]);
            

            
        
        }
    }

    public function getErrorReports()
    {
        // Fetch all the error reports from the database
        $errorReports = PlacementErrorReport::all();
        // Pass the error reports to the view
        return view('mainPage', ['errorReports' => $errorReports]);
    }
    


}
