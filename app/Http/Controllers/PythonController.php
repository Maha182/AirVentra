<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;
use App\Models\PlacementErrorReport;

class PythonController extends Controller
{
    public function sendLocationData(Request $request)
    {
        $client = new Client();
        $redirectTo = request()->input('redirect_to', 'storage-assignment'); 

        // Fetch barcode from Python
        $barcodeResponse = $client->get('http://127.0.0.1:5000/get_barcode');
        $barcodeData = json_decode($barcodeResponse->getBody()->getContents(), true);
        $productID = $barcodeData['barcode'] ?? null;

        if (!$productID) {
            // If no barcode, redirect to the specified page and pass error message
            if ($redirectTo == 'mainPage') {
                return redirect()->route('mainPage');
            }
    
            // Default redirect for other cases
            return redirect()->route($redirectTo);
        }if ($productID) {
            // Store productID in session
            session(['productID' => $productID]);
        }

        // Fetch product from the database
        $product = Product::where('id', $productID)->first();

        if (!$product) {
            return redirect()->route($redirectTo)->with('error', 'Product not found');
        }

        // $description = $product->description;

        // // // Send description to the Python API
        // $response = $client->post('http://127.0.0.1:5001/getData', [
        //     'json' => ['description' => $description]
        // ]);

        // $result = json_decode($response->getBody()->getContents(), true);
        // $zone_name = $result['zone_name'] ?? null;
        $zone_name = 'Dry Zone';

        return $this->assignLocation($product, $zone_name, $redirectTo);
    }

    private function assignLocation($product, $zone_name, $redirectTo)
    {
        if (!$product) {
            return redirect()->route($redirectTo)->with('error', 'Product not found.');

        }

        // Query for an available location in the specified zone
        $location = Location::where('zone_name', $zone_name)
            ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
            ->orderBy('aisle')
            ->first();

        if (!$location) {
            session()->forget('assigned_product');
            return redirect()->route($redirectTo)->with('error', 'No available storage locations in zone: ' . $zone_name);
        }


            $errors = PlacementErrorReport::with('product')->get();

        // Store product assignment in session
        session()->put('assigned_product', [
            'product_id' => $product->id,
            'product_name' => $product->title,
            'product_description' => $product->description,
            'product_quantity' => $product->quantity,
            'location' => $product->location_id,
            'assigned_location' => $location->id,
            'zone_name' => $location->zone_name,
            'aisle' => $location->aisle,
            'rack' => $location->rack,
            'current_capacity' => $location->current_capacity,
            'capacity' => $location->capacity,
            'errors' => $errors->map(function ($error) {
                return [
                    'wrong_location' => $error->wrong_location,
                    'correct_location' => $error->correct_location, // Access the correct location here
                    'product_id' => $error->product->id,
                    'status' =>$error->status,
                ];
            }),
        ]);

        return response()->json([
            'assigned_product' => session('assigned_product'),
            'success' => true,
            'redirect' => route($redirectTo)
        ]);
    }


    public function assignProductToLocation(Request $request)
    {
        // Retrieve product_id and location_id from session
        $product_id = session('assigned_product.product_id');
        $location_id = session('assigned_product.assigned_location');

        // Ensure product_id and location_id exist in session
        if (!$product_id || !$location_id) {
            return redirect()->route('storage-assignment')->with('error', 'No assigned product or location found in session.');
        }

        $product = Product::find($product_id);
        $location = Location::find($location_id);

        // Ensure valid product and location instances
        if (!$product) {
            return redirect()->route('storage-assignment')->with('error', 'Product not found.');
        }

        if (!$location) {
            return redirect()->route('storage-assignment')->with('error', 'Location not found.');
        }

        if ($product->location_id == $location->id) {
            return redirect()->route('storage-assignment')->with('info', 'This product is already assigned to this location.');
        }

        // Prevent assigning if current_capacity is already at maximum
        if ((int) $location->current_capacity >= (int) $location->capacity) {
            return redirect()->route('storage-assignment')->with('error', 'Location is at full capacity.');
        }

        // Assign location to product
        $product->location_id = $location->id;
        $product->save();

        $location->increment('current_capacity');

        session()->flash('assigned_product', [
            'product_id' => $product->id,
            'product_name' => $product->title,
            'product_description' => $product->description,
            'product_quantity' => $product->quantity,
            'assigned_location' => $location->id,
            'zone_name' => $location->zone_name,
            'aisle' => $location->aisle,
            'rack' => $location->rack,
            'current_capacity' => $location->current_capacity,
            'capacity' => $location->capacity
        ]);

        return redirect()->route('storage-assignment')->with('success', 'Product Assigned Successfully');
    }


    public function clearSession(Request $request)
{
    $request->session()->forget(['assigned_product']);
    return response()->json(['message' => 'Session cleared']);
}

}

