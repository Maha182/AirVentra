<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Location;

class PythonController extends Controller
{
    public function sendLocationData()
    {
        $client = new Client();
        $productID = 'M1'; 
    
        // Fetch product from the database
        $product = Product::where('id', $productID)->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $description = $product->description;

        // Send description to the Python API
        $response = $client->post('http://127.0.0.1:5000/getData', [
            'json' => ['description' => $description]
        ]);

        // Get response from Python
        $result = json_decode($response->getBody()->getContents(), true);
        $zone_name = $result['zone_name'] ?? null;

        // Call fetchDataFromPython and pass the zone name
        return $this->fetchDataFromPython($product, $zone_name);
    }

    public function fetchDataFromPython($product, $zone_name)
    {
        if (!$product) {
            return redirect()->route('storage-assignment')->with('error', 'Product not found');
        }

        // Query for location in the specified zone with available capacity
        $location = Location::where('zone_name', $zone_name)
            ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
            ->orderBy('aisle')
            ->first();

        if (!$location) {
            return redirect()->route('storage-assignment')->with('error', 'No available storage locations in zone: ' . $zone_name);
        }
        
        // Assign product to location
        session()->put('assigned_product', [
            'product_id' => $product->id,
            'assigned_location' => $location->id,
            'zone_name' => $location->zone_name
        ]);
        return redirect()->route('storage-assignment');
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

    // Fetch product and location as single model instances
    $product = Product::find($product_id);
    $location = Location::find($location_id);

    // Ensure valid product and location instances
    if (!$product) {
        return redirect()->route('storage-assignment')->with('error', 'Product not found.');
    }

    if (!$location) {
        return redirect()->route('storage-assignment')->with('error', 'Location not found.');
    }

    // Assign location to product
    $product->location_id = $location->id;
    $product->save();

    // Update location capacity
    $location->increment('current_capacity');

    // Update session with the new data
    session()->put('assigned_product', [
        'product_id' => $product->id,
        'assigned_location' => $location->id,
        'zone_name' => $location->zone_name
    ]);

    return redirect()->route('storage-assignment')->with('success', 'Product Assigned Successfully');
}


}
