<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;
use App\Models\LocationCheck;

use App\Models\PlacementErrorReport;
use App\Http\Controllers\PythonController;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PlacementErrorMail;

class PlacementController extends Controller
{
    
    public function checkPlacement(Request $request)
    {
        $productID = session('productID');
        $product = Product::find($productID);

        if (!$product) {
            \Log::error('Product not found in database', ['product_id' => $productID]);
            return response()->json(['error' => 'Product not found.'], 400);
        }

        $fixedLocationId = 'L0008'; // Example: 'L0008' is your fixed location ID

        // Fetch the fixed location from the database
        $location = Location::find($fixedLocationId);
        if (!$location) {
            \Log::error('Fixed Location not found in database', ['location_id' => $fixedLocationId]);
            return response()->json(['error' => 'Fixed Location not found.'], 400);
        }

        // Correct location for the product
        $correctLocation = $product->location_id;

        if ($location->id === $correctLocation) {
            // Mark the barcode as processed
            session()->push('processed_barcodes', $productID);
            return response()->json(['success' => 'The product is in the correct place.']);
        } else {
            \Log::info('Product in wrong location', ['product_id' => $productID, 'wrong_location' => $location->id, 'correct_location' => $correctLocation]);

            // Log the error in placement_error table
            PlacementErrorReport::create([
                'product_id' => $productID,  // Use correct variable
                'scan_date' => Carbon::now(),
                'wrong_location' => $correctLocation,
                'correct_location' =>$location->id ,
                'status' => 'Pending'
            ]);
            

            // Send an email notification
            $emailData = [
                'product' => $product,
                'wrong_location' => $correctLocation,
                'correct_location' => $location->id
            ];

            Mail::to('maha1822003@gmail.com')->send(new PlacementErrorMail($emailData));

            // Fetch any errors related to this product
            $errors = LocationCheck::where('product_id', $productID)->get();


            // Mark the barcode as processed
            session()->push('processed_barcodes', $productID);
            $errorReports = PlacementErrorReport::all();

            // Return the error reports as a JSON response
            return response()->json([
                'assigned_product' => session('assigned_product'),
                'success' => true,
                'error' => $errorReports,
                'wrong_location' => $correctLocation,
                'correct_location' => $location->id
            ], 400);
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
