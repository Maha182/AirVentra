<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use App\Models\Location;
use App\Models\Product;
use Illuminate\Support\Facades\Session;


class PythonController extends Controller
{
    public function receiveData(Request $request)
{
    \Log::info('Received Data:', $request->all()); // Debugging

    $data = $request->json()->all(); // Retrieve JSON data from Python request

    if (!isset($data['id']) || !isset($data['zone_name'])) {
        return response()->json(['error' => 'Missing productID or zone_name in request'], 400);
    }

    $product = Product::where('id', $data['id'])->first();

    if (!$product) {
        return response()->json(['error' => 'Product with ID ' . $data['id'] . ' not found'], 404);
    }

    // Query for location
    $location = Location::where('zone_name', $data['zone_name'])
        ->whereRaw('CAST(current_capacity AS SIGNED) < CAST(capacity AS SIGNED)')
        ->orderBy('aisle')
        ->first();

    if (!$location) {
        return response()->json(['error' => 'No available storage locations in zone: ' . $data['zone_name']], 404);
    }

    // Assign the location to the product
    $product->location_id = $location->locationID;
    $product->save();

    // Update capacity
    $location->increment('current_capacity');

    // Store result in session to display on the page
    Session::put('storage_info', [
        'product_id' => $product->id,
        'assigned_location' => $location->locationID,
        'zone_name' => $location->zone_name
    ]);

    return response()->json([
        'message' => 'Storage assigned successfully!',
        'product_id' => $product->id,
        'assigned_location' => $location->locationID,
        'zone_name' => $location->zone_name
    ]);
}

}
